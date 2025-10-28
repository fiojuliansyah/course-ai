@extends('admin.layouts.app')

@section('content')
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Error!</strong> Tolong periksa form.
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <a href="{{ route('admin.courses.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Kembali ke Daftar Kursus</a>
                    <h1 class="text-3xl font-extrabold text-gray-900 mt-2">Kelola Silabus: {{ $course->title }}</h1>
                </div>
                
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                    <button id="add-module-btn" class="flex items-center bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-green-700 transition duration-150">
                        <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Modul Manual
                    </button>
                    <button id="ai-toggle-btn" class="flex items-center bg-purple-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-purple-700 transition duration-150">
                        Generate Modul AI
                    </button>
                </div>
            </div>

            <div id="ai-form-container" class="bg-white shadow-xl rounded-xl p-6 border border-gray-100 mb-8 hidden">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Otomatisasi Modul dengan AI (PDF Upload)</h3>
                <p class="text-sm text-gray-600 mb-4">Unggah file PDF materi Anda. AI akan menganalisis konten dan membuat struktur Modul kursus secara otomatis.</p>
                
                <form id="ai-generate-form" data-course-id="{{ $course->id }}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"> 
                    
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File PDF</label>
                    <input type="file" name="material_file" accept=".pdf" required class="w-full p-3 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                    
                    <button type="submit" id="generate-ai-btn" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md transition duration-150 flex items-center justify-center">
                        Generate Modul dari PDF
                        <svg id="ai-loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </form>
            </div>
            
            <div class="space-y-4">
                @forelse ($modules as $module)
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                        
                        <div class="w-full text-left p-5 font-bold text-gray-800 flex justify-between items-center">
                            <button type="button" class="flex-1 module-header text-left" data-module-id="{{ $module->id }}">
                                {{ $module->title }}
                            </button>
                            <div class="flex items-center space-x-3">
                                <span class="text-indigo-600 hover:text-indigo-800 text-sm font-medium edit-module-btn cursor-pointer" data-id="{{ $module->id }}" data-type="module">
                                    Edit Modul
                                </span>
                                <button class="delete-module-btn text-red-600 hover:text-red-800" data-id="{{ $module->id }}" data-title="{{ $module->title }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>

                        <div id="module-content-{{ $module->id }}" class="border-t border-gray-100 space-y-3 p-5 module-content hidden">
                            @forelse ($module->materials as $material)
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    
                                    <div class="mb-2 sm:mb-0 sm:w-2/3">
                                        <span class="text-gray-700 font-semibold block">{{ $material->title }}</span>
                                        
                                        @if ($material->content)
                                            <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                                {!! Str::limit(strip_tags($material->content), 150) !!} 
                                            </p>
                                        @endif
                                    </div>
                                    
                                    <div class="flex items-center space-x-3 mt-1 sm:mt-0">
                                        
                                        @if ($material->content)
                                            <button 
                                                type="button"
                                                class="view-content-btn text-sm text-indigo-600 hover:text-indigo-800 transition font-medium"
                                                data-title="{{ $material->title }}"
                                                data-content="{{ $material->content }}">
                                                Lihat Dokumen
                                            </button>
                                            
                                            <button class="edit-module-btn text-green-600 hover:text-green-800 text-sm font-medium" data-id="{{ $material->id }}" data-type="material">
                                                Edit
                                            </button>
                                        @endif
                                        
                                        @if ($material->file_path)
                                            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-sm text-green-600 hover:text-green-800 transition">Lihat File</a>
                                        @else
                                            <label for="file-{{ $material->id }}" class="flex items-center text-xs font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer transition">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                                Upload File
                                                <input id="file-{{ $material->id }}" type="file" class="sr-only" onchange="alert('File untuk: {{ $material->title }} terpilih!')">
                                            </label>
                                        @endif

                                        <button class="delete-material-btn text-red-600 hover:text-red-800" data-id="{{ $material->id }}" data-title="{{ $material->title }}">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Modul ini belum memiliki materi.</p>
                            @endforelse
                            
                            <button type="button" class="mt-4 text-indigo-600 hover:text-indigo-800 text-sm font-medium add-material-btn p-5 pt-0" data-module-id="{{ $module->id }}" data-module-title="{{ $module->title }}">
                                <svg class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Tambah Materi
                            </button>
                        </div>

                    </div>
                @empty
                    <p class="text-center text-gray-500 bg-white p-5 rounded-xl shadow-lg border">Belum ada modul yang dibuat untuk kursus ini.</p>
                @endforelse
            </div>

        </div>
    </main>
    
    {{-- MODAL UNTUK MENAMPILKAN DOKUMEN KONTEN --}}
    <div id="content-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-8 border w-11/12 md:w-3/5 lg:w-1/2 shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
            <h3 id="content-modal-title" class="text-xl font-extrabold text-gray-900 mb-4 border-b pb-2"></h3>
            
            <div id="content-modal-body" class="prose max-w-none text-gray-700">
                
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" id="close-content-modal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Tutup</button>
            </div>
        </div>
    </div>
    
    {{-- MODAL EDIT MODULE/MATERIAL --}}
    <div id="edit-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-8 border w-11/12 md:w-3/5 lg:w-1/2 shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
            <h3 id="edit-modal-title" class="text-xl font-extrabold text-gray-900 mb-4 border-b pb-2">Edit Data</h3>
            
            <form id="edit-form" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="type" id="edit-type-field">
                <input type="hidden" name="id" id="edit-id-field">

                <div id="edit-form-content" class="space-y-4">
                    
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="close-edit-modal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- MODAL TAMBAH MODUL MANUAL --}}
    <div id="add-module-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-8 border w-11/12 md:w-1/3 shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
            <h3 class="text-xl font-extrabold text-gray-900 mb-4 border-b pb-2">Tambah Modul Baru</h3>
            
            <form id="add-module-form" action="{{ route('admin.modules.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <div class="space-y-4">
                    <div>
                        <label for="module_title" class="block text-sm font-medium text-gray-700">Judul Modul</label>
                        <input type="text" id="module_title" name="title" required class="w-full p-2 border rounded mt-1" placeholder="Contoh: Modul 3: Pemformatan Lanjutan">
                    </div>
                    <div>
                        <label for="module_description" class="block text-sm font-medium text-gray-700">Deskripsi (Opsional)</label>
                        <textarea id="module_description" name="description" rows="3" class="w-full p-2 border rounded mt-1" placeholder="Ringkasan singkat tentang isi modul."></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="close-add-module-modal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Simpan Modul</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- MODAL TAMBAH MATERI MANUAL --}}
    <div id="add-material-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-8 border w-11/12 md:w-1/2 shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
            <h3 id="add-material-title" class="text-xl font-extrabold text-gray-900 mb-4 border-b pb-2">Tambah Materi Baru</h3>
            
            <form id="add-material-form" action="{{ route('admin.materials.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="module_id" id="material-module-id">

                <div class="space-y-4">
                    <div>
                        <label for="material_title" class="block text-sm font-medium text-gray-700">Judul Materi</label>
                        <input type="text" id="material_title" name="title" required class="w-full p-2 border rounded mt-1" placeholder="Contoh: 3.1 Penggunaan Tabel Markdown">
                    </div>
                    <div>
                        <label for="material_content" class="block text-sm font-medium text-gray-700">Konten Awal (Opsional - Markdown)</label>
                        <textarea id="material_content" name="content" rows="6" class="w-full p-2 border rounded mt-1" placeholder="Isi konten materi ini (Markdown). Bisa diedit nanti."></textarea>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" id="close-add-material-modal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Batal</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Simpan Materi</button>
                </div>
            </form>
        </div>
    </div>
    
    {{-- MODAL KONFIRMASI DELETE MATERI BARU --}}
    <div id="delete-material-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Hapus Materi</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus materi: **<span id="modal-material-title" class="font-semibold text-gray-700"></span>**? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="items-center px-4 py-3 sm:flex sm:flex-row-reverse">
                
                <form id="delete-material-form" method="POST" class="sm:ml-3 sm:text-sm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus Permanen
                    </button>
                </form>

                <button type="button" id="cancel-material-delete-btn" class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI DELETE MODUL --}}
<div id="delete-module-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Hapus Modul</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Apakah Anda yakin ingin menghapus modul: **<span id="modal-module-title" class="font-semibold text-gray-700"></span>**? Semua materi di dalamnya akan ikut terhapus.
                </p>
            </div>
            <div class="items-center px-4 py-3 sm:flex sm:flex-row-reverse">
                
                <form id="delete-module-form" method="POST" class="sm:ml-3 sm:text-sm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Hapus Permanen
                    </button>
                </form>

                <button type="button" id="cancel-module-delete-btn" class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const moduleHeaders = document.querySelectorAll('.module-header');
            const aiToggleBtn = document.getElementById('ai-toggle-btn');
            const formContainer = document.getElementById('ai-form-container');
            const generateForm = document.getElementById('ai-generate-form');
            const generateButton = document.getElementById('generate-ai-btn');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

            const contentModal = document.getElementById('content-modal');
            const contentModalTitle = document.getElementById('content-modal-title');
            const contentModalBody = document.getElementById('content-modal-body');
            const closeContentModalBtn = document.getElementById('close-content-modal');
            
            const editModal = document.getElementById('edit-modal');
            const closeEditModalBtn = document.getElementById('close-edit-modal');
            const editModalTitle = document.getElementById('edit-modal-title');
            const editFormContent = document.getElementById('edit-form-content');
            const editForm = document.getElementById('edit-form');
            const editButtons = document.querySelectorAll('.edit-module-btn');

            const addModuleBtn = document.getElementById('add-module-btn');
            const addModuleModal = document.getElementById('add-module-modal');
            const closeAddModuleModalBtn = document.getElementById('close-add-module-modal');
            
            const addMaterialButtons = document.querySelectorAll('.add-material-btn');
            const addMaterialModal = document.getElementById('add-material-modal');
            const materialModuleIdField = document.getElementById('material-module-id');
            const addMaterialTitle = document.getElementById('add-material-title');
            const closeAddMaterialModalBtn = document.getElementById('close-add-material-modal');
            
            const deleteMaterialModal = document.getElementById('delete-material-modal');
            const deleteMaterialButtons = document.querySelectorAll('.delete-material-btn');
            const modalMaterialTitle = document.getElementById('modal-material-title');
            const deleteMaterialForm = document.getElementById('delete-material-form');
            const cancelMaterialDeleteBtn = document.getElementById('cancel-material-delete-btn');
            
            const deleteModuleModal = document.getElementById('delete-module-modal');
            const deleteModuleButtons = document.querySelectorAll('.delete-module-btn');
            const modalModuleTitle = document.getElementById('modal-module-title');
            const deleteModuleForm = document.getElementById('delete-module-form');
            const cancelModuleDeleteBtn = document.getElementById('cancel-module-delete-btn');

            const moduleBaseUrl = '{{ url('admin/modules') }}';
            const materialBaseUrl = '{{ url('admin/materials') }}';


            function toggleModal(modalId, show) {
                const modal = document.getElementById(modalId);
                if (!modal) return;
                if (show) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                } else {
                    modal.classList.remove('flex');
                    modal.classList.add('hidden');
                }
            }
            
            function fetchAndFillForm(id, type) {
                const baseUrl = (type === 'module') ? moduleBaseUrl : materialBaseUrl;
                const url = `${baseUrl}/${id}/edit`; 
                
                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error(`Gagal mengambil data ${type}: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        editModalTitle.textContent = `Edit ${type === 'module' ? 'Modul' : 'Materi'}: ${data.title}`;
                        editForm.action = `${baseUrl}/${id}`;
                        
                        if (type === 'module') {
                            editFormContent.innerHTML = `
                                <label class="block text-sm font-medium text-gray-700">Judul Modul</label>
                                <input type="text" name="title" value="${data.title || ''}" class="w-full p-2 border rounded">
                                <label class="block text-sm font-medium text-gray-700 mt-3">Deskripsi</label>
                                <textarea name="description" rows="3" class="w-full p-2 border rounded">${data.description || ''}</textarea>
                                <label class="block text-sm font-medium text-gray-700 mt-3">Urutan</label>
                                <input type="number" name="order" value="${data.order || 0}" class="w-full p-2 border rounded">
                            `;
                        } else {
                            editFormContent.innerHTML = `
                                <label class="block text-sm font-medium text-gray-700">Judul Materi</label>
                                <input type="text" name="title" value="${data.title || ''}" class="w-full p-2 border rounded">
                                <label class="block text-sm font-medium text-gray-700 mt-3">Konten (Markdown)</label>
                                <textarea name="content" rows="10" class="w-full p-2 border rounded">${data.content || ''}</textarea>
                                <label class="block text-sm font-medium text-gray-700 mt-3">Urutan</label>
                                <input type="number" name="order" value="${data.order || 0}" class="w-full p-2 border rounded">
                            `;
                        }
                        
                        document.getElementById('edit-type-field').value = type;
                        document.getElementById('edit-id-field').value = id;

                        toggleModal('edit-modal', true);
                    })
                    .catch(error => {
                        alert('Gagal memuat data edit: ' + error.message);
                        console.error('Fetch Error:', error);
                    });
            }

            moduleHeaders.forEach(header => {
                const contentDiv = document.getElementById('module-content-' + header.getAttribute('data-module-id'));
                if (contentDiv && !contentDiv.classList.contains('hidden')) {
                    contentDiv.classList.add('hidden');
                }

                header.addEventListener('click', function() {
                    toggleModal('ai-form-container', false);
                    contentDiv.classList.toggle('hidden');
                });
            });
            
            if (aiToggleBtn && formContainer) {
                aiToggleBtn.addEventListener('click', function() {
                    formContainer.classList.toggle('hidden');
                });
            }

            if (generateForm) {
                generateForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const courseId = this.getAttribute('data-course-id');
                    
                    generateButton.disabled = true;
                    generateButton.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memproses AI...';

                    fetch(`/admin/courses/${courseId}/material/generate`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken 
                        }
                    })
                    .then(response => {
                         if (!response.ok) {
                            return response.json().then(errorData => {
                                throw new Error(errorData.error || `Server returned status: ${response.status}`);
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
                        alert('Terjadi kesalahan pada proses AI: ' + error.message);
                    })
                    .finally(() => {
                        generateButton.disabled = false;
                        generateButton.innerHTML = '<svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>Generate Modul dari PDF';
                    });
                });
            }
            
            const viewContentButtons = document.querySelectorAll('.view-content-btn');
            
            viewContentButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const title = this.getAttribute('data-title');
                    const contentMarkdown = this.getAttribute('data-content');
                    
                    contentModalTitle.textContent = title;
                    contentModalBody.innerHTML = marked.parse(contentMarkdown); 
                    
                    toggleModal('content-modal', true);
                });
            });
            
            closeContentModalBtn.addEventListener('click', () => toggleModal('content-modal', false));
            contentModal.addEventListener('click', (e) => {
                if (e.target === contentModal) toggleModal('content-modal', false);
            });
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    fetchAndFillForm(id, type);
                });
            });

            closeEditModalBtn.addEventListener('click', () => toggleModal('edit-modal', false));
            editModal.addEventListener('click', (e) => {
                if (e.target === editModal) toggleModal('edit-modal', false);
            });
            
            if (addModuleBtn && addModuleModal) {
                addModuleBtn.addEventListener('click', function() {
                    toggleModal('add-module-modal', true);
                });
                closeAddModuleModalBtn.addEventListener('click', () => toggleModal('add-module-modal', false));
                addModuleModal.addEventListener('click', (e) => {
                    if (e.target === addModuleModal) toggleModal('add-module-modal', false);
                });
            }
            
            if (addMaterialButtons && addMaterialModal) {
                addMaterialButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const moduleId = this.getAttribute('data-module-id');
                        const moduleTitle = this.getAttribute('data-module-title');

                        materialModuleIdField.value = moduleId;
                        addMaterialTitle.textContent = `Tambah Materi Baru ke ${moduleTitle}`;
                        
                        toggleModal('add-material-modal', true);
                    });
                });

                closeAddMaterialModalBtn.addEventListener('click', () => toggleModal('add-material-modal', false));
                addMaterialModal.addEventListener('click', (e) => {
                    if (e.target === addMaterialModal) toggleModal('add-material-modal', false);
                });
            }

            // --- LOGIC DELETE MODUL/MATERI ---
            
            if (deleteMaterialButtons.length > 0) {
                deleteMaterialButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const materialId = this.getAttribute('data-id');
                        const materialTitle = this.getAttribute('data-title');
                        
                        modalMaterialTitle.textContent = materialTitle;
                        deleteMaterialForm.action = `${materialBaseUrl}/${materialId}`;
                        
                        toggleModal('delete-material-modal', true);
                    });
                });

                cancelMaterialDeleteBtn.addEventListener('click', () => toggleModal('delete-material-modal', false));
                deleteMaterialModal.addEventListener('click', (e) => {
                    if (e.target === deleteMaterialModal) toggleModal('delete-material-modal', false);
                });
            }
            
            if (deleteModuleButtons.length > 0) {
                deleteModuleButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const moduleId = this.getAttribute('data-id');
                        const moduleTitle = this.getAttribute('data-title');
                        
                        modalModuleTitle.textContent = moduleTitle;
                        deleteModuleForm.action = `${moduleBaseUrl}/${moduleId}`;
                        
                        toggleModal('delete-module-modal', true);
                    });
                });

                cancelModuleDeleteBtn.addEventListener('click', () => toggleModal('delete-module-modal', false));
                deleteModuleModal.addEventListener('click', (e) => {
                    if (e.target === deleteModuleModal) toggleModal('delete-module-modal', false);
                });
            }

        });
    </script>
@endpush