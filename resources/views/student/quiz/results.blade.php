@extends('student.layouts.app')

@section('content')
    <main class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        
        @php
            // Ambil data untuk menghindari error jika session hilang
            $results = $results ?? ['totalScore' => 0, 'correctCount' => 0, 'totalQuestions' => 0, 'maxPoints' => 0, 'passingScore' => 0, 'passed' => false];
        @endphp

        <div class="mt-10 border-t pt-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Review Jawaban</h2>
            
            <div class="space-y-6">
                @forelse ($detailedAnswers as $answer)
                    @php
                        $q = $answer->question;
                        $isCorrect = $answer->is_correct;
                        $answerClass = $isCorrect ? 'border-green-400 bg-green-50' : 'border-red-400 bg-red-50';
                        $answerIcon = $isCorrect ? '✅' : '❌';
                        
                        // DECODING AI RESPONSE DARI DATABASE
                        $aiResponseData = json_decode($answer->ai_response, true);
                        $aiFeedbackText = Arr::get($aiResponseData, 'feedback');
                    @endphp

                    <div class="p-4 border-l-4 {{ $answerClass }} rounded-lg shadow-sm">
                        <p class="text-sm font-semibold text-gray-800">{{ $loop->iteration }}. {{ $q->text }}</p>
                        
                        <p class="mt-2 text-xs font-medium {{ $isCorrect ? 'text-green-700' : 'text-red-700' }}">
                            {{ $answerIcon }} Skor Anda: {{ $answer->score }} / {{ $q->points }} Poin
                        </p>
                        
                        {{-- Detail Jawaban Siswa --}}
                        <p class="mt-2 text-sm font-medium text-gray-700">Jawaban Anda:</p>
                        <blockquote class="pl-3 border-l-2 border-gray-400 italic text-sm text-gray-600 mt-1">
                            {{ $q->type === 'multiple' ? 'Pilihan Ganda' : $answer->student_response }}
                        </blockquote>
                        
                        {{-- FEEDBACK AI UNIVERSAL --}}
                        @if ($aiFeedbackText)
                            <div class="mt-3 p-3 bg-white border rounded-lg">
                                <p class="text-xs font-semibold text-indigo-600">Feedback Penilaian AI:</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $aiFeedbackText }}</p>
                            </div>
                        @endif

                        @if (!$isCorrect && $q->type !== 'essay')
                            {{-- Kunci Jawaban untuk Choice yang salah --}}
                            <p class="mt-2 text-xs text-gray-500">Kunci: <span class="font-semibold text-green-700">{{ $q->options[(int)$q->correct_answer] ?? 'N/A' }}</span></p>
                        @endif

                    </div>
                @empty
                    <p class="text-center text-gray-500">Tidak ada jawaban yang tersimpan.</p>
                @endforelse
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="{{ route('student.courses.quiz.show', $course) }}" class="bg-indigo-600 text-white px-6 py-3 rounded-lg text-lg hover:bg-indigo-700 font-semibold">
                Coba Kuis Lagi
            </a>
        </div>
    </main>
@endsection