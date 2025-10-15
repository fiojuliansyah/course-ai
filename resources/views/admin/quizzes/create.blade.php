@extends('admin.layouts.app')

@section('content')
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong> Tolong periksa input formulir.
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <a href="{{ route('admin.courses.edit', $course) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Kembali ke Edit Kursus</a>
            
            <div class="flex justify-between items-center mt-2 mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900">Kelola Pertanyaan: {{ $course->title }}</h1>
                
                <div class="flex space-x-3 items-center">
                    
                    <div class="flex items-center space-x-2">
                        <label for="num_questions" class="text-sm font-medium text-gray-700">Jumlah:</label>
                        <input type="number" id="num_questions" value="3" min="1" max="10" class="w-16 p-1 border border-gray-300 rounded-lg text-center shadow-sm">
                    </div>
                    
                    <button type="button" id="generate-ai-questions-btn" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-yellow-700 transition flex items-center justify-center">
                        Generate AI
                        <svg id="quiz-loading-spinner" class="animate-spin -ml-1 ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                    
                    <button type="button" id="toggle-create-form" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-indigo-700 transition">
                        Tambah Pertanyaan Baru
                    </button>
                </div>
            </div>
            
            <div id="create-form-container" class="hidden">
                <form action="{{ route('admin.courses.quizzes.store', $course) }}" method="POST" class="space-y-8 mb-8">
                    @csrf
                    
                    <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Detail Pertanyaan Baru</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                                <select id="question-type" name="type" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="single" {{ old('type') == 'single' ? 'selected' : '' }}>Pilihan Tunggal (Single Choice)</option>
                                    <option value="multiple" {{ old('type') == 'multiple' ? 'selected' : '' }}>Pilihan Ganda (Multiple Choice)</option>
                                    <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>Esai/Uraian (Essay)</option>
                                </select>
                            </div>
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700">Bobot Nilai</label>
                                <input type="number" id="points" name="points" value="{{ old('points', 1) }}" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label for="text" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
                            <textarea id="text" name="text" rows="4" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">{{ old('text') }}</textarea>
                        </div>
                        
                        <div id="answer-options-container">
                        </div>
                        
                    </div>
                    
                    <div class="flex justify-end space-x-4">
                        <button type="button" id="cancel-create-form" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</button>
                        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-indigo-700 transition">Simpan Pertanyaan</button>
                    </div>
                </form>
            </div>


            <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Pertanyaan Tersedia ({{ $course->questions->count() }})</h2>
            
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pertanyaan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opsi Jawaban</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kunci Jawaban</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($course->questions as $question)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 truncate max-w-xs">{{ Str::limit($question->text, 80) }}</td>
                                    
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        @if ($question->options)
                                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                                @foreach ($question->options as $option)
                                                    <li>{{ Str::limit($option, 30) }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @if ($question->type === 'single')
                                            @php $index = (int) $question->correct_answer; @endphp
                                            <span class="font-medium text-green-700">{{ $question->options[$index] ?? 'N/A' }}</span>
                                        
                                        @elseif ($question->type === 'multiple' && $question->options)
                                            @php 
                                                $correctIndices = json_decode($question->correct_answer, true); 
                                            @endphp
                                            
                                            @if ($correctIndices)
                                                <ul class="list-disc list-inside space-y-0.5 text-xs">
                                                    @foreach ($correctIndices as $index)
                                                        <li>{{ Str::limit($question->options[$index] ?? 'Jawaban', 30) }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                Tidak ada kunci
                                            @endif
                                            
                                        @elseif ($question->type === 'essay')
                                            <span class="italic text-gray-500">{{ Str::limit($question->correct_answer, 30) }}</span>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if ($question->type === 'essay') bg-yellow-100 text-yellow-800
                                            @elseif ($question->type === 'multiple') bg-purple-100 text-purple-800
                                            @else bg-blue-100 text-blue-800
                                            @endif">
                                            {{ ucfirst($question->type) }}
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-500">{{ $question->points }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <button class="text-red-600 hover:text-red-900">Hapus</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-3 text-center text-gray-500">Kursus ini belum memiliki pertanyaan kuis.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection
@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('answer-options-container');
            const typeSelector = document.getElementById('question-type');
            const toggleButton = document.getElementById('toggle-create-form');
            const createFormContainer = document.getElementById('create-form-container');
            const cancelButton = document.getElementById('cancel-create-form');
            const generateAiBtn = document.getElementById('generate-ai-questions-btn');
            const quizSpinner = document.getElementById('quiz-loading-spinner');
            const numQuestionsInput = document.getElementById('num_questions');

            let optionIndex = 0;
            const courseId = '{{ $course->id }}';

            function renderOptions() {
                const type = typeSelector.value;
                container.innerHTML = '';
                optionIndex = 0;

                if (type === 'single' || type === 'multiple') {
                    container.innerHTML += `
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Pilihan Jawaban (Minimal 2)</h3>
                        <div id="options-list" class="space-y-3 mb-4"></div>
                        <button type="button" id="add-option-btn" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg text-sm font-medium hover:bg-indigo-200 transition">
                            Tambah Pilihan
                        </button>
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Kunci Jawaban</h3>
                        <div id="correct-answer-area" class="space-y-3 mb-4"></div>
                    `;
                    
                    for (let i = 0; i < 3; i++) {
                        addOption();
                    }
                    
                    document.getElementById('add-option-btn').addEventListener('click', addOption);
                } else if (type === 'essay') {
                    container.innerHTML = `
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Kunci Jawaban (Model)</h3>
                        <textarea name="correct_essay" rows="6" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm" placeholder="Masukkan jawaban model untuk perbandingan penilaian.">{{ old('correct_essay') }}</textarea>
                    `;
                }
            }

            function addOption(initialValue = '') {
                const optionsList = document.getElementById('options-list');
                const correctAnswerArea = document.getElementById('correct-answer-area');
                const type = typeSelector.value;
                const index = optionIndex++;

                const optionDiv = document.createElement('div');
                optionDiv.className = 'flex space-x-3 items-center';
                optionDiv.innerHTML = `
                    <input type="text" name="options[${index}]" placeholder="Teks Pilihan Jawaban" class="flex-1 p-2 border border-gray-300 rounded-lg" value="${initialValue}">
                    <button type="button" class="remove-option-btn text-red-500 hover:text-red-700">Hapus</button>
                `;
                optionsList.appendChild(optionDiv);

                let keyInput = '';
                if (type === 'single') {
                    keyInput = `
                        <div class="flex items-center space-x-3" data-index="${index}">
                            <input type="radio" name="correct_single" value="${index}" class="text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            <label class="text-sm text-gray-700">${optionsList.children.length}. ${optionDiv.querySelector('input').value || 'Pilihan Baru'}</label>
                        </div>
                    `;
                } else if (type === 'multiple') {
                    keyInput = `
                        <div class="flex items-center space-x-3" data-index="${index}">
                            <input type="checkbox" name="correct_multiple[]" value="${index}" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4">
                            <label class="text-sm text-gray-700">${optionsList.children.length}. ${optionDiv.querySelector('input').value || 'Pilihan Baru'}</label>
                        </div>
                    `;
                }
                correctAnswerArea.innerHTML += keyInput;

                optionDiv.querySelector('input').addEventListener('input', function() {
                    const label = correctAnswerArea.querySelector(`div[data-index="${index}"] label`);
                    if (label) {
                        label.textContent = `${index + 1}. ${this.value}`;
                    }
                });

                optionDiv.querySelector('.remove-option-btn').addEventListener('click', function() {
                    optionDiv.remove();
                    correctAnswerArea.querySelector(`div[data-index="${index}"]`).remove();
                });
            }

            typeSelector.addEventListener('change', renderOptions);
            
            renderOptions();
            
            toggleButton.addEventListener('click', function() {
                createFormContainer.classList.toggle('hidden');
                if (!createFormContainer.classList.contains('hidden')) {
                    document.getElementById('question-type').focus();
                }
            });
            
            cancelButton.addEventListener('click', function() {
                createFormContainer.classList.add('hidden');
            });
            
            if (generateAiBtn) {
                generateAiBtn.addEventListener('click', function() {
                    const count = numQuestionsInput.value || 3;
                    const originalText = generateAiBtn.textContent;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    
                    if (count < 1 || count > 10) {
                        alert("Jumlah pertanyaan minimal 1 dan maksimal 10.");
                        return;
                    }

                    generateAiBtn.disabled = true;
                    quizSpinner.classList.remove('hidden');
                    generateAiBtn.innerHTML = '<span class="mr-2">Memproses...</span>'; 

                    fetch(`/admin/courses/${courseId}/quizzes/generate`, {
                        method: 'POST',
                        body: JSON.stringify({ num_questions: count }),
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json' 
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.error || `Server Error: ${response.status}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        alert(data.message);
                        window.location.reload(); 
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal menghasilkan pertanyaan: ' + error.message);
                    })
                    .finally(() => {
                        generateAiBtn.disabled = false;
                        quizSpinner.classList.add('hidden');
                        generateAiBtn.innerHTML = originalText;
                    });
                });
            }
        });
    </script>
@endpush