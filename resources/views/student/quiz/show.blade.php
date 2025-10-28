@extends('student.layouts.app')

@section('content')
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Kuis: {{ $course->title }}</h1>
            <p class="text-gray-600 mb-6">Jawab semua pertanyaan untuk menyelesaikan kuis ini.</p>

            <form id="quiz-form" method="POST" action="{{ route('student.courses.quiz.submit', $course) }}">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                    
                    {{-- KOLOM KIRI: PERTANYAAN AKTIF --}}
                    <div class="lg:col-span-3 space-y-8" id="questions-container">
                        
                        @forelse ($questions as $question)
                            <div class="quiz-question-card bg-white shadow-lg rounded-xl p-6 border border-gray-100" 
                                 data-question-id="{{ $question->id }}"
                                 data-question-index="{{ $loop->index }}"
                                 @if (!$loop->first) style="display: none;" @endif>
                                
                                <p class="text-lg font-semibold text-gray-800 mb-3">
                                    {{ $loop->iteration }}. {{ $question->text }} 
                                    <span class="text-sm text-indigo-600 font-normal ml-2">({{ $question->points }} Poin)</span>
                                </p>
                                
                                <input type="hidden" name="question_ids[]" value="{{ $question->id }}">

                                {{-- Render Opsi Jawaban --}}
                                @if ($question->type === 'single' || $question->type === 'multiple')
                                    <div class="space-y-3 mt-4">
                                        @foreach ($question->options as $index => $option)
                                            <label class="flex items-start space-x-3 cursor-pointer p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                                @if ($question->type === 'single')
                                                    <input type="radio" name="q_{{ $question->id }}" value="{{ $index }}" 
                                                           class="mt-1 h-5 w-5 text-indigo-600 focus:ring-indigo-500"
                                                           {{ isset($answers[$question->id]) && (int)$answers[$question->id] === $index ? 'checked' : '' }}>
                                                @else
                                                    @php 
                                                        // Decode jawaban multiple choice dari JSON string yang tersimpan di DB
                                                        $savedAnswers = isset($answers[$question->id]) ? json_decode($answers[$question->id], true) : [];
                                                    @endphp
                                                    <input type="checkbox" name="q_{{ $question->id }}[]" value="{{ $index }}" 
                                                           class="mt-1 rounded h-5 w-5 text-indigo-600 focus:ring-indigo-500"
                                                           {{ is_array($savedAnswers) && in_array($index, $savedAnswers) ? 'checked' : '' }}>
                                                @endif
                                                <span class="text-gray-700 text-sm">{{ $option }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif ($question->type === 'essay')
                                    <div class="mt-4">
                                        <textarea name="q_{{ $question->id }}" rows="6" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm" placeholder="Tulis jawaban Anda di sini.">{{ $answers[$question->id] ?? '' }}</textarea>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center text-gray-500">Tidak ada pertanyaan kuis yang tersedia saat ini.</p>
                        @endforelse

                        {{-- NAVIGATION BUTTONS --}}
                        <div class="flex justify-between pt-4 border-t">
                            <button type="button" id="prev-question" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold transition hidden">&larr; Sebelumnya</button>
                            <button type="button" id="next-question" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold transition ml-auto">Selanjutnya &rarr;</button>
                            
                            {{-- SUBMIT BUTTON (Dinamis: Muncul di akhir) --}}
                            <button type="submit" id="submit-quiz" class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold transition ml-auto hidden flex items-center justify-center">
                                <span id="submit-text">Kirim Jawaban</span>
                                <svg id="submit-spinner" class="animate-spin h-5 w-5 ml-2 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- KOLOM KANAN: NAVIGASI GRID & TIMER --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white shadow-xl rounded-xl p-6 border border-indigo-100 sticky top-20">
                            <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Waktu & Navigasi</h2>
                            
                            {{-- Timer --}}
                            <div class="text-center mb-6">
                                <p class="text-sm font-medium text-gray-600">Sisa Waktu Ujian:</p>
                                <div id="quiz-timer" class="text-4xl font-extrabold text-red-600 mt-2">
                                    30:00
                                </div>
                            </div>

                            {{-- Navigation Grid --}}
                            <div id="nav-grid" class="grid grid-cols-5 gap-3 mt-4 border-t pt-4">
                                @if ($questions->count() > 0)
                                    @foreach ($questions as $question)
                                        <button type="button" 
                                                data-index="{{ $loop->index }}"
                                                data-question-id="{{ $question->id }}"
                                                class="nav-btn bg-gray-100 text-gray-700 p-2 rounded-lg text-sm font-semibold transition duration-150 
                                                @if ($loop->first) bg-indigo-600 text-white @endif">
                                            {{ $loop->iteration }}
                                        </button>
                                    @endforeach
                                @else
                                    <p class="col-span-5 text-center text-gray-500 text-sm">Kuis kosong.</p>
                                @endif
                            </div>
                            
                            <div class="mt-6">
                                <span class="block text-xs text-gray-500">Warna: <span class="text-indigo-600">Aktif</span>, <span class="text-green-600">Dijawab</span>, <span class="text-gray-700">Belum Jawab</span></span>
                            </div>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('quiz-form');
            const questionCards = document.querySelectorAll('.quiz-question-card');
            const navButtons = document.querySelectorAll('.nav-btn');
            const timerDisplay = document.getElementById('quiz-timer');
            const prevBtn = document.getElementById('prev-question');
            const nextBtn = document.getElementById('next-question');
            const submitBtn = document.getElementById('submit-quiz');
            const submitText = document.getElementById('submit-text');
            const submitSpinner = document.getElementById('submit-spinner');
            
            let currentQuestionIndex = 0;
            const totalQuestions = questionCards.length;
            const totalTimeSeconds = 30 * 60; 
            let timeLeft = totalTimeSeconds;

            if (totalQuestions === 0) return;

            // --- FUNGSI TIMER ---
            function formatTime(seconds) {
                const minutes = Math.floor(seconds / 60);
                const remainingSeconds = seconds % 60;
                return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
            }

            function startTimer() {
                const timerInterval = setInterval(() => {
                    timeLeft--;
                    timerDisplay.textContent = formatTime(timeLeft);

                    if (timeLeft <= 300) { 
                        timerDisplay.classList.add('text-yellow-600');
                        timerDisplay.classList.remove('text-red-600');
                    }
                    if (timeLeft <= 60) { 
                        timerDisplay.classList.remove('text-yellow-600');
                        timerDisplay.classList.add('text-red-600');
                    }

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        alert('Waktu ujian telah habis! Jawaban Anda akan dikirim otomatis.');
                        form.submit();
                    }
                }, 1000);
            }

            // --- FUNGSI NAVIGASI & DISPLAY ---
            function showQuestion(index) {
                // Sembunyikan semua kartu pertanyaan
                questionCards.forEach(card => card.style.display = 'none');
                
                // Set index baru dan tampilkan kartu yang relevan
                currentQuestionIndex = index;
                if (questionCards[currentQuestionIndex]) {
                    questionCards[currentQuestionIndex].style.display = 'block';
                }
                
                updateNavigationUI();
            }

            function isQuestionAnswered(questionId) {
                // Mengambil elemen pertanyaan berdasarkan data-question-id
                const questionElement = document.querySelector(`.quiz-question-card[data-question-id="${questionId}"]`);
                if (!questionElement) return false;

                const inputs = questionElement.querySelectorAll('input, textarea');
                
                for (const input of inputs) {
                    // Check radio/checkbox
                    if ((input.type === 'radio' || input.type === 'checkbox') && input.checked) return true;
                    // Check textarea
                    if (input.tagName === 'TEXTAREA' && input.value.trim() !== '') return true;
                }
                return false;
            }

            function updateNavigationUI() {
                // Update button visibility
                prevBtn.classList.toggle('hidden', currentQuestionIndex === 0);
                nextBtn.classList.toggle('hidden', currentQuestionIndex === totalQuestions - 1);
                
                // Show/Hide Submit button
                submitBtn.classList.toggle('hidden', currentQuestionIndex !== totalQuestions - 1);
                
                // Update navigation grid status
                navButtons.forEach(btn => {
                    const index = parseInt(btn.getAttribute('data-index'));
                    const questionId = btn.getAttribute('data-question-id');
                    const answered = isQuestionAnswered(questionId);

                    // Reset classes
                    btn.classList.remove('bg-indigo-600', 'text-white', 'bg-green-600', 'text-gray-100');
                    btn.classList.add('bg-gray-100', 'text-gray-700'); // Default color

                    if (index === currentQuestionIndex) {
                        btn.classList.add('bg-indigo-600', 'text-white'); // Active
                        btn.classList.remove('bg-gray-100', 'text-gray-700');
                    } else if (answered) {
                        btn.classList.add('bg-green-600', 'text-gray-100'); // Answered
                        btn.classList.remove('bg-gray-100', 'text-gray-700');
                    }
                });
            }

            // --- EVENT LISTENERS ---
            prevBtn.addEventListener('click', () => {
                if (currentQuestionIndex > 0) showQuestion(currentQuestionIndex - 1);
            });

            nextBtn.addEventListener('click', () => {
                if (currentQuestionIndex < totalQuestions - 1) showQuestion(currentQuestionIndex + 1);
            });
            
            navButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const index = parseInt(this.getAttribute('data-index'));
                    showQuestion(index);
                });
            });

            // Re-check status whenever input changes (for dynamic coloring)
            form.addEventListener('change', updateNavigationUI);
            form.addEventListener('input', updateNavigationUI);

            // Listener SUBMIT FORM (Untuk Animasi)
            form.addEventListener('submit', function(e) {
                // Tampilkan animasi
                submitBtn.disabled = true;
                submitText.textContent = 'Mengirim...';
                submitSpinner.classList.remove('hidden');
                
                // Hentikan timer saat form dikirim
                // Note: Jika timerInterval didefinisikan secara global, Anda bisa menghentikannya di sini.
            });

            // Initial setup
            showQuestion(0); // Panggil showQuestion(0) untuk memastikan pertanyaan pertama terlihat
            startTimer();
        });
    </script>
@endpush