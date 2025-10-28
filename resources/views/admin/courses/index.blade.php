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
                <h1 class="text-3xl font-extrabold text-gray-900">Daftar Kursus</h1>
                
                {{-- TOMBOL GLOBAL: Tambah Kursus & Tambah Pertanyaan --}}
                <div class="flex space-x-3">
                    {{-- Tombol Tambah Pertanyaan Baru (Global) --}}
                    <a href="{{ route('admin.courses.quizzes.create', ['course' => 'new']) }}" class="flex items-center bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-yellow-700 transition duration-150">
                        <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.228a1.5 1.5 0 100 2.121 1.5 1.5 0 000-2.121zM10.586 11.586a1.5 1.5 0 002.121-2.121 1.5 1.5 0 00-2.121 2.121zM14.828 9.228a1.5 1.5 0 000 2.121 1.5 1.5 0 000-2.121zM12 21h-2a1 1 0 01-1-1v-2h4v2a1 1 0 01-1 1h-2z" /></svg>
                        Tambah Pertanyaan
                    </a>
                    
                    <a href="{{ route('admin.courses.create') }}" class="flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-indigo-700 transition duration-150">
                        <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Kursus Baru
                    </a>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 space-y-4 sm:space-y-0 sm:space-x-4">
                <input type="text" placeholder="Cari kursus..." class="w-full sm:w-1/3 p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                <div class="flex w-full sm:w-auto space-x-4">
                    <select class="p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option>Semua Kategori</option>
                    </select>
                    <select class="p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option>Status: Semua</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

                @forelse ($courses as $course)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition duration-300 hover:shadow-2xl hover:scale-[1.01]">
                        <img class="w-full h-40 object-cover" src="{{ $course->thumbnail_path ? asset('storage/' . $course->thumbnail_path) : 'https://via.placeholder.com/600x400/9333ea/ffffff?text=E-Course' }}" alt="{{ $course->title }}">
                        <div class="p-5">
                            <span class="inline-block px-3 py-1 text-xs font-medium {{ $course->status == 'active' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} rounded-full mb-2">{{ strtoupper($course->status) }}</span>
                            <span class="inline-block px-3 py-1 text-xs font-medium text-indigo-700 bg-indigo-100 rounded-full mb-2 ml-2">{{ $course->category->name ?? 'Tanpa Kategori' }}</span>

                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $course->title }}</h3>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $course->short_description }}</p>
                            
                            <div class="flex justify-between items-center text-sm mb-4 border-t pt-3">
                                <div class="text-gray-500">
                                    Siswa: <span class="font-semibold text-gray-800">{{ number_format($course->students_count) }}</span>
                                </div>
                                <div class="text-gray-500">
                                    Harga: <span class="font-semibold text-indigo-600">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="flex space-x-2 justify-between">
                                <a href="{{ route('admin.courses.edit', $course) }}" class="flex-1 bg-indigo-100 text-indigo-700 py-2 rounded-lg text-sm font-medium hover:bg-indigo-200 transition text-center">Edit</a>
                                
                                <a href="{{ route('admin.courses.material.index', $course) }}" class="flex-1 bg-green-100 text-green-700 py-2 rounded-lg text-sm font-medium hover:bg-green-200 transition text-center">Material</a>
                                
                                <a href="{{ route('admin.courses.quizzes.create', $course) }}" class="flex-1 bg-yellow-100 text-yellow-700 py-2 rounded-lg text-sm font-medium hover:bg-yellow-200 transition text-center">
                                    Kuis
                                </a>
                                
                                <button class="delete-course-btn p-2 rounded-lg text-red-600 hover:bg-red-50 transition" 
                                    data-course-id="{{ $course->id }}" 
                                    data-course-title="{{ $course->title }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-500">Belum ada kursus yang terdaftar.</p>
                @endforelse

            </div>
            
            <div class="mt-10 flex justify-center">
                {{ $courses->links('pagination::tailwind') }}
            </div>

        </div>
    </main>
    
    {{-- MODAL DELETE (Kode tidak diubah) --}}
    <div id="delete-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white max-h-full overflow-y-auto">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Konfirmasi Hapus Kursus</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus kursus: **<span id="modal-course-title" class="font-semibold text-gray-700"></span>**? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="items-center px-4 py-3 sm:flex sm:flex-row-reverse">
                    
                    <form id="delete-form" method="POST" class="sm:ml-3 sm:text-sm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" id="confirm-delete-btn-final" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Hapus Permanen
                        </button>
                    </form>

                    <button type="button" id="cancel-delete-btn" class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        const deleteModal = document.getElementById('delete-modal');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const deleteButtons = document.querySelectorAll('.delete-course-btn');
        const modalCourseTitle = document.getElementById('modal-course-title');
        const deleteForm = document.getElementById('delete-form');
        
        const courseDestroyBaseUrl = '{{ url('admin/courses') }}'; 

        // 1. Tampilkan Modal saat tombol Hapus diklik
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); 
                const courseId = this.getAttribute('data-course-id');
                const courseTitle = this.getAttribute('data-course-title');
                
                modalCourseTitle.textContent = courseTitle;
                deleteForm.action = `${courseDestroyBaseUrl}/${courseId}`;
                
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex'); 
            });
        });

        // 2. Sembunyikan Modal saat tombol Batal diklik
        cancelDeleteBtn.addEventListener('click', function() {
            deleteModal.classList.remove('flex');
            deleteModal.classList.add('hidden');
        });

        // 3. Sembunyikan Modal saat klik di luar area modal
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.classList.remove('flex');
                deleteModal.classList.add('hidden');
            }
        });
    </script>
@endpush