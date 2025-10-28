<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use DB;
use Exception;
use Log;

class StudentQuizController extends Controller
{
    const AI_TIMEOUT = 30; 

    public function showQuiz(Course $course)
    {
        $questions = $course->questions; 
        
        $answers = StudentAnswer::where('user_id', Auth::id())
                                ->where('course_id', $course->id)
                                ->pluck('student_response', 'question_id');

        return view('student.quiz.show', compact('course', 'questions', 'answers'));
    }

    public function submitQuiz(Request $request, Course $course)
    {
        $userId = Auth::id();
        $questions = $course->questions;
        $totalScore = 0;
        $correctCount = 0;

        DB::beginTransaction();
        try {
            foreach ($questions as $question) {
                $responseKey = 'q_' . $question->id;
                $studentResponse = $request->input($responseKey);
                
                // Menerima 4 nilai: Score, isCorrect, DB Response, AI Payload JSON String
                [$score, $isCorrect, $dbResponse, $aiResponsePayload] = $this->gradeQuestion($question, $studentResponse);

                StudentAnswer::updateOrCreate(
                    ['user_id' => $userId, 'question_id' => $question->id, 'course_id' => $course->id],
                    [
                        'student_response' => $dbResponse,
                        'score' => $score,
                        'is_correct' => $isCorrect,
                        'ai_response' => $aiResponsePayload, // Menyimpan respons AI (JSON string)
                    ]
                );

                $totalScore += $score;
                if ($isCorrect) $correctCount++;
            }
            
            DB::commit();

            return redirect()->route('student.courses.quiz.results', $course)
                 ->with(['totalScore' => $totalScore, 'correctCount' => $correctCount]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Quiz Submission Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Gagal menyimpan jawaban: ' . $e->getMessage());
        }
    }

    protected function gradeQuestion(Question $question, $studentResponse)
    {
        $maxPoints = $question->points;
        $score = 0;
        $isCorrect = false;
        $dbResponse = $studentResponse;
        $aiResponsePayload = null;

        if ($question->type === 'single') {
            $isCorrect = ((int)$studentResponse === (int)$question->correct_answer);
            $score = $isCorrect ? $maxPoints : 0;
            $dbResponse = (string)$studentResponse;

        } elseif ($question->type === 'multiple') {
            $correctAnswers = json_decode($question->correct_answer, true) ?? [];
            $studentAnswers = (array)$studentResponse;

            $isCorrect = empty(array_diff($correctAnswers, $studentAnswers)) && 
                         empty(array_diff($studentAnswers, $correctAnswers));
            
            $score = $isCorrect ? $maxPoints : 0;
            $dbResponse = json_encode($studentAnswers);

        } elseif ($question->type === 'essay') {
            try {
                // Penilaian Esai
                [$aiScore, $aiFeedbackPayload] = $this->gradeEssayByAI($question, $studentResponse);
                $score = $aiScore;
                $isCorrect = ($aiScore === $maxPoints);
                $dbResponse = (string)$studentResponse;
                $aiResponsePayload = json_encode($aiFeedbackPayload); // Simpan payload lengkap

            } catch (Exception $e) {
                Log::error("AI Essay Grading Failed for QID {$question->id}: " . $e->getMessage());
                $score = 0; 
                $isCorrect = false;
                $dbResponse = (string)$studentResponse; 
            }
        }

        return [$score, $isCorrect, $dbResponse, $aiResponsePayload];
    }

    protected function gradeEssayByAI(Question $question, string $studentAnswer): array
    {
        $apiKey = env('OPENAI_API_KEY');
        $maxPoints = $question->points;
        $modelAnswer = $question->correct_answer;

        $prompt = "Anda adalah penguji yang ketat. Beri skor (0 hingga {$maxPoints}) untuk JAWABAN SISWA berdasarkan KUNCI JAWABAN MODEL. Berikan juga penjelasan singkat. Asumsi Jawaban Model adalah sempurna.\n\n"
                . "KUNCI JAWABAN MODEL:\n{$modelAnswer}\n\n"
                . "JAWABAN SISWA:\n{$studentAnswer}\n\n"
                . "OUTPUT FORMAT JSON:\n"
                . "{\"score\": integer, \"feedback\": string}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(self::AI_TIMEOUT)
          ->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4-turbo', 
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => 500,
            'temperature' => 0.1,
            'response_format' => ['type' => 'json_object'],
        ]);

        if (!$response->successful()) {
            throw new Exception("OpenAI connection failed or returned status: " . $response->status());
        }

        $responseData = $response->json();
        $content = Arr::get($responseData, 'choices.0.message.content');
        $payload = json_decode(trim($content, " \n\r\t\v\0"), true);
        
        if (json_last_error() !== JSON_ERROR_NONE || !isset($payload['score'])) {
            throw new Exception("Invalid JSON structure from AI.");
        }
        
        $finalScore = min($maxPoints, max(0, (int)Arr::get($payload, 'score', 0)));
        
        return [$finalScore, $payload]; 
    }


    public function showResults(Course $course)
    {
        $totalScore = session('totalScore');
        $correctCount = session('correctCount');

        if (is_null($totalScore) || is_null($correctCount)) {
            $answers = StudentAnswer::where('user_id', Auth::id())
                                    ->where('course_id', $course->id)
                                    ->with('question')
                                    ->get();
            
            $totalScore = $answers->sum('score');
            $correctCount = $answers->where('is_correct', true)->count();
        }
        
        $maxPoints = $course->questions->sum('points');
        $passingScore = $maxPoints * 0.7; 
        $passed = $totalScore >= $passingScore;
        
        $results = [
            'totalScore' => $totalScore,
            'correctCount' => $correctCount,
            'totalQuestions' => $course->questions->count(),
            'maxPoints' => $maxPoints,
            'passingScore' => $passingScore,
            'passed' => $passed
        ];

        $detailedAnswers = StudentAnswer::where('user_id', Auth::id())
                                    ->where('course_id', $course->id)
                                    ->with('question')
                                    ->get();

        return view('student.quiz.results', compact('course', 'results', 'detailedAnswers'));
    }
}