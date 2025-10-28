<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Exception;
use Log;

class QuizController extends Controller
{
    public function index(Course $course)
    {
        $course->load('questions');
        return view('admin.quizzes.index', compact('course'));
    }
    
    public function create(Course $course)
    {
        $course->load('questions', 'modules.materials');
        return view('admin.quizzes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['single', 'multiple', 'essay'])],
            'text' => 'required|string',
            'points' => 'required|integer|min:1',
            'options' => 'nullable|array|required_if:type,single,multiple',
            'options.*' => 'nullable|string|max:255',
            'correct_single' => 'nullable|integer|required_if:type,single',
            'correct_multiple' => 'nullable|array|required_if:type,multiple',
            'correct_essay' => 'nullable|string|required_if:type,essay',
        ]);

        $options = $request->input('options', []);
        
        if ($validated['type'] == 'single') {
            $correctAnswer = $request->input('correct_single');
        } elseif ($validated['type'] == 'multiple') {
            $correctAnswer = json_encode($request->input('correct_multiple', []));
        } else {
            $correctAnswer = $request->input('correct_essay');
        }

        Question::create([
            'course_id' => $course->id,
            'type' => $validated['type'],
            'text' => $validated['text'],
            'points' => $validated['points'],
            'options' => array_values(array_filter($options)),
            'correct_answer' => $correctAnswer,
        ]);

        return redirect()->route('admin.courses.quizzes.create', $course)
                         ->with('success', 'Pertanyaan berhasil ditambahkan!');
    }
    
    public function generateQuestions(Request $request, Course $course)
    {
        set_time_limit(120);

        $numQuestions = (int) $request->json('num_questions', 3);
        $numQuestions = max(1, min(10, $numQuestions)); 

        $course->load(['modules.materials']);
        $allContent = '';
        
        foreach ($course->modules as $module) {
            foreach ($module->materials as $material) {
                if ($material->content) {
                    $allContent .= "### Modul: " . $module->title . " - Materi: " . $material->title . "\n" . $material->content . "\n\n";
                }
            }
        }
        
        if (empty($allContent)) {
            return response()->json(['error' => 'Tidak ada konten materi yang tersedia di kursus ini untuk menghasilkan pertanyaan.'], 400);
        }

        $limitedContent = substr($allContent, 0, 150000); 
        $courseTitle = $course->title;

        try {
            $prompt = "Berdasarkan konten kursus berikut, buatkan {$numQuestions} pertanyaan kuis. Tipe pertanyaan harus bervariasi secara acak antara 'single', 'multiple', atau 'essay'. Tentukan bobot nilai ('points') antara 1 sampai 5 (sesuaikan dengan tingkat kesulitan: 1-2 mudah, 3-4 sedang, 5 sulit).\n\n--- ATURAN JAWABAN ---\n- Single/Multiple Choice: Wajib 4 opsi jawaban. Indeks jawaban yang benar ('correct_index') harus angka integer 0 sampai 3.\n- Essay: Berikan 'correct_answer' berupa string model jawaban yang lengkap.\n\nKonten: \n\n--- KONTEN KURSUS: {$courseTitle} ---\n{$limitedContent}\n\n--- INSTRUKSI OUTPUT ---\nHasilkan output dalam format JSON VALID, yang merupakan **ARRAY UTAMA** dari objects pertanyaan (JANGAN gunakan kunci parent seperti 'questions' atau 'quizzes'). Keys yang WAJIB ada di setiap object: 'type' (string: single/multiple/essay), 'text', 'options' (array of 4 strings, jika choice), 'correct_index' (integer 0-3, jika choice), 'correct_answer' (string, jika essay), dan 'points' (integer 1-5).";

            $apiKey = env('OPENAI_API_KEY');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(90)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 2000,
                'temperature' => 0.7,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI API Error', ['status' => $response->status(), 'response' => $response->body()]);
                return response()->json(['error' => 'OpenAI API Error: Gagal mendapatkan respons dari server.'], 502);
            }

            $content = Arr::get($response->json(), 'choices.0.message.content', '');
            $payload = json_decode(trim($content, " \n\r\t\v\0"), true);
            
            $questionArray = [];
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                 if (isset($payload['questions']) && is_array($payload['questions'])) {
                     $questionArray = $payload['questions'];
                 } elseif (isset($payload['quizzes']) && is_array($payload['quizzes'])) {
                     $questionArray = $payload['quizzes'];
                 } else {
                     Log::error('AI Question JSON Parsing Failed.', ['content' => $content]);
                     return response()->json(['error' => 'Gagal memproses JSON dari AI. Silakan coba lagi.'], 500);
                 }
            } else {
                $questionArray = $payload;
            }
            
            if (empty($questionArray) || !is_array($questionArray)) {
                 return response()->json(['error' => 'Output JSON AI kosong atau tidak mengandung array pertanyaan yang valid.'], 500);
            }

            $count = 0;
            foreach ($questionArray as $q) {
                $type = Arr::get($q, 'type');
                $text = Arr::get($q, 'text') ?? Arr::get($q, 'question');
                $points = Arr::get($q, 'points', 1);
                
                $options = null;
                $correctAnswer = null;
                $isValid = false;

                if (in_array($type, ['single', 'multiple'])) {
                    $options = Arr::get($q, 'options');
                    $correctIndexRaw = Arr::get($q, 'correct_index') ?? Arr::get($q, 'answer_index');
                    $correctIndex = is_numeric($correctIndexRaw) ? (int)$correctIndexRaw : null;
                    
                    if (is_array($options) && count($options) >= 2 && !is_null($correctIndex) && $correctIndex >= 0 && $correctIndex < count($options)) {
                        $correctAnswer = $correctIndex;
                        $isValid = true;
                    } else {
                        Log::warning('Gagal validasi data Choice AI.', ['data' => $q]);
                    }
                } elseif ($type === 'essay') {
                    $correctAnswer = Arr::get($q, 'correct_answer') ?? Arr::get($q, 'answer_model');
                    if (!empty($correctAnswer)) {
                        $isValid = true;
                    } else {
                        Log::warning('Gagal validasi data Essay AI (Jawaban Kosong).', ['data' => $q]);
                    }
                }

                if ($text && $isValid) {
                    Question::create([
                        'course_id' => $course->id,
                        'type' => $type,
                        'text' => $text,
                        'points' => max(1, min(5, (int)$points)),
                        'options' => array_values(array_filter($options ?? [])), 
                        'correct_answer' => $correctAnswer,
                    ]);
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil menambahkan $count pertanyaan baru dari AI!",
            ], 201);

        } catch (Exception $e) {
            Log::error('Generate Questions Fatal Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
    
    public function update(Request $request, Course $course, Question $question)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['single', 'multiple', 'essay'])],
            'text' => 'required|string',
            'points' => 'required|integer|min:1',
            'options' => 'nullable|array|required_if:type,single,multiple',
            'options.*' => 'nullable|string|max:255',
            'correct_single' => 'nullable|integer|required_if:type,single',
            'correct_multiple' => 'nullable|array|required_if:type,multiple',
            'correct_essay' => 'nullable|string|required_if:type,essay',
        ]);

        $options = $request->input('options', []);
        
        if ($validated['type'] == 'single') {
            $correctAnswer = $request->input('correct_single');
        } elseif ($validated['type'] == 'multiple') {
            $correctAnswer = json_encode($request->input('correct_multiple', []));
        } else {
            $correctAnswer = $request->input('correct_essay');
        }

        $question->update([
            'type' => $validated['type'],
            'text' => $validated['text'],
            'points' => $validated['points'],
            'options' => array_values(array_filter($options)),
            'correct_answer' => $correctAnswer,
        ]);
        
        return redirect()->route('admin.courses.quizzes.create', $course)->with('success', 'Pertanyaan berhasil diperbarui.');
    }
    
    public function destroy(Course $course, Question $question)
    {
        $question->delete();
        return redirect()->route('admin.courses.quizzes.create', $course)->with('success', 'Pertanyaan berhasil dihapus.');
    }
}