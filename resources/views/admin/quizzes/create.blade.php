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
                                @php
                                    $correctMultiple = ($question->type === 'multiple' && $question->correct_answer) ? json_decode($question->correct_answer, true) : null;
                                    $questionData = json_encode([
                                        'id' => $question->id,
                                        'type' => $question->type,
                                        'text' => $question->text,
                                        'points' => $question->points,
                                        'options' => $question->options ?? [],
                                        'correct_answer' => $question->correct_answer,
                                        'correct_multiple' => $correctMultiple,
                                    ]);
                                @endphp
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
                                            @if ($correctMultiple)
                                                <ul class="list-disc list-inside space-y-0.5 text-xs">
                                                    @foreach ($correctMultiple as $index)
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
                                        <button type="button" 
                                            data-question='{{ $questionData }}'
                                            class="edit-question-btn text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </button>
                                        <button type="button" 
                                            data-id="{{ $question->id }}"
                                            data-text="{{ Str::limit($question->text, 50) }}"
                                            class="delete-question-btn text-red-600 hover:text-red-900 ml-3">
                                            Hapus
                                        </button>
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
    
    <div id="edit-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto hidden" style="z-index: 999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 relative">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Edit Pertanyaan</h3>
                
                <form id="edit-question-form" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <label for="edit_type" class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                            <select id="edit_type" name="type" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="single">Pilihan Tunggal</option>
                                <option value="multiple">Pilihan Ganda</option>
                                <option value="essay">Esai/Uraian</option>
                            </select>
                        </div>
                        <div>
                            <label for="edit_points" class="block text-sm font-medium text-gray-700">Bobot Nilai</label>
                            <input type="number" id="edit_points" name="points" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="edit_text" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
                        <textarea id="edit_text" name="text" rows="4" required class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>
                    
                    <div id="edit-answer-options-container">
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-4 border-t">
                        <button type="button" id="cancel-edit-form" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</button>
                        <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-green-700 transition">Perbarui Pertanyaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div id="delete-modal" class="fixed inset-0 bg-gray-600 bg-opacity-75 overflow-y-auto hidden" style="z-index: 999;">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6 relative">
                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 text-red-600">Konfirmasi Hapus</h3>
                
                <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus pertanyaan ini?</p>
                <p class="text-sm italic text-gray-500 mb-6" id="delete-question-text"></p>

                <form id="delete-question-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-4">
                        <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow-md hover:bg-red-700 transition">Hapus Permanen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createContainer = document.getElementById('answer-options-container');
            const createTypeSelector = document.getElementById('question-type');
            const toggleButton = document.getElementById('toggle-create-form');
            const createFormContainer = document.getElementById('create-form-container');
            const cancelButton = document.getElementById('cancel-create-form');
            const generateAiBtn = document.getElementById('generate-ai-questions-btn');
            const quizSpinner = document.getElementById('quiz-loading-spinner');
            const numQuestionsInput = document.getElementById('num_questions');

            const editModal = document.getElementById('edit-modal');
            const editForm = document.getElementById('edit-question-form');
            const editContainer = document.getElementById('edit-answer-options-container');
            const editTypeSelector = document.getElementById('edit_type');
            const editCancelButton = document.getElementById('cancel-edit-form');
            
            const deleteModal = document.getElementById('delete-modal');
            const deleteForm = document.getElementById('delete-question-form');
            const deleteText = document.getElementById('delete-question-text');

            let optionIndex = 0;
            const courseId = '{{ $course->id }}';

            function renderOptions(container, typeSelector, optionsData = [], correctAnswerData = null, isEdit = false) {
                const type = typeSelector.value;
                container.innerHTML = '';
                let listId = isEdit ? 'edit-options-list' : 'options-list';
                let keyAreaId = isEdit ? 'edit-correct-answer-area' : 'correct-answer-area';
                
                let initialOptions = optionsData.length > 0 ? optionsData : (isEdit ? [] : ['', '', '']);

                optionIndex = 0;

                if (type === 'single' || type === 'multiple') {
                    container.innerHTML += `
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Pilihan Jawaban (Min. 2)</h3>
                        <div id="${listId}" class="space-y-3 mb-4"></div>
                        <button type="button" id="${isEdit ? 'edit-add-option-btn' : 'add-option-btn'}" class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-lg text-sm font-medium hover:bg-indigo-200 transition">
                            Tambah Pilihan
                        </button>
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Kunci Jawaban</h3>
                        <div id="${keyAreaId}" class="space-y-3 mb-4"></div>
                    `;
                    
                    const listElement = document.getElementById(listId);
                    
                    initialOptions.forEach(function(optionText) {
                        addOption(listElement, document.getElementById(keyAreaId), type, optionIndex++, optionText, correctAnswerData, isEdit);
                    });
                    
                    if (!isEdit && initialOptions.length === 0) {
                         for (let i = 0; i < 3; i++) {
                            addOption(listElement, document.getElementById(keyAreaId), type, optionIndex++, '', correctAnswerData, isEdit);
                        }
                    }

                    document.getElementById(isEdit ? 'edit-add-option-btn' : 'add-option-btn').addEventListener('click', function() {
                        addOption(listElement, document.getElementById(keyAreaId), type, optionIndex++, '', correctAnswerData, isEdit);
                    });
                } else if (type === 'essay') {
                    container.innerHTML = `
                        <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2 mt-6">Kunci Jawaban (Model)</h3>
                        <textarea name="correct_essay" rows="6" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm" placeholder="Masukkan jawaban model untuk perbandingan penilaian.">${correctAnswerData || ''}</textarea>
                    `;
                }
            }

            function addOption(optionsList, correctAnswerArea, type, index, initialValue = '', correctAnswerData = null, isEdit = false) {
                const namePrefix = 'options';
                const keyName = type === 'single' ? 'correct_single' : 'correct_multiple[]';
                
                const optionDiv = document.createElement('div');
                optionDiv.className = 'flex space-x-3 items-center';
                optionDiv.setAttribute('data-index-val', index); 
                optionDiv.innerHTML = `
                    <input type="text" name="${namePrefix}[${index}]" placeholder="Teks Pilihan Jawaban" class="flex-1 p-2 border border-gray-300 rounded-lg" value="${initialValue}">
                    <button type="button" class="remove-option-btn text-red-500 hover:text-red-700">Hapus</button>
                `;
                optionsList.appendChild(optionDiv);

                let keyInput = '';
                let isChecked = false;

                if (type === 'single') {
                    isChecked = correctAnswerData == index;
                    keyInput = `
                        <div class="flex items-center space-x-3" data-index="${index}">
                            <input type="radio" name="${keyName}" value="${index}" class="text-indigo-600 focus:ring-indigo-500 h-4 w-4" ${isChecked ? 'checked' : ''}>
                            <label class="text-sm text-gray-700">${index + 1}. ${initialValue || 'Pilihan Baru'}</label>
                        </div>
                    `;
                } else if (type === 'multiple') {
                    isChecked = Array.isArray(correctAnswerData) && correctAnswerData.includes(index);
                    keyInput = `
                        <div class="flex items-center space-x-3" data-index="${index}">
                            <input type="checkbox" name="${keyName}" value="${index}" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" ${isChecked ? 'checked' : ''}>
                            <label class="text-sm text-gray-700">${index + 1}. ${initialValue || 'Pilihan Baru'}</label>
                        </div>
                    `;
                }
                
                if (keyInput) {
                    correctAnswerArea.innerHTML += keyInput;
                }

                optionDiv.querySelector('input').addEventListener('input', function() {
                    const label = correctAnswerArea.querySelector(`div[data-index="${index}"] label`);
                    if (label) {
                        label.textContent = `${index + 1}. ${this.value}`;
                    }
                });

                optionDiv.querySelector('.remove-option-btn').addEventListener('click', function() {
                    optionDiv.remove();
                    const keyElement = correctAnswerArea.querySelector(`div[data-index="${index}"]`);
                    if (keyElement) keyElement.remove();
                });
            }

            createTypeSelector.addEventListener('change', function() {
                renderOptions(createContainer, createTypeSelector);
            });
            renderOptions(createContainer, createTypeSelector);

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

                    fetch(`{{ route('admin.courses.quizzes.generate', $course) }}`, {
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
            
            document.querySelectorAll('.edit-question-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const questionData = JSON.parse(this.dataset.question);
                    
                    editForm.action = `{{ route('admin.courses.quizzes.update', ['course' => $course->id, 'question' => 'QUESTION_ID']) }}`.replace('QUESTION_ID', questionData.id);
                    
                    document.getElementById('edit_points').value = questionData.points;
                    document.getElementById('edit_text').value = questionData.text;
                    
                    editTypeSelector.value = questionData.type;
                    
                    let correctAnswer;
                    if (questionData.type === 'single') {
                        correctAnswer = parseInt(questionData.correct_answer);
                    } else if (questionData.type === 'multiple') {
                        // Jika null dari blade, pastikan jadi array kosong di sini
                        correctAnswer = questionData.correct_multiple || [];
                    } else if (questionData.type === 'essay') {
                        correctAnswer = questionData.correct_answer;
                    }
                    
                    renderOptions(editContainer, editTypeSelector, questionData.options, correctAnswer, true);
                    
                    editModal.classList.remove('hidden');
                    
                    editTypeSelector.onchange = function() {
                        renderOptions(editContainer, editTypeSelector, questionData.options, correctAnswer, true);
                    };
                });
            });
            
            editCancelButton.addEventListener('click', function() {
                editModal.classList.add('hidden');
            });
            
            document.querySelectorAll('.delete-question-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const questionId = this.dataset.id;
                    const questionText = this.dataset.text;
                    
                    deleteForm.action = `{{ route('admin.courses.quizzes.destroy', ['course' => $course->id, 'question' => 'QUESTION_ID']) }}`.replace('QUESTION_ID', questionId);
                    
                    deleteText.textContent = `"${questionText}..."`;
                    
                    deleteModal.classList.remove('hidden');
                });
            });
        });
    </script>
@endpush