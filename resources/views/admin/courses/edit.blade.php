@extends('admin.layouts.app')

@section('content')
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Gagal menyimpan perubahan!</strong>
                <span class="block sm:inline">Tolong periksa kembali input Anda.</span>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Edit Kursus: {{ $course->title }}</h1>
            
            <form action="{{ route('admin.courses.update', $course) }}" method="POST" class="space-y-8" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Detail Dasar Kursus</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label for="judul" class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                                <input type="text" id="judul" name="title" value="{{ old('title', $course->title) }}" placeholder="Contoh: Tailwind CSS for Beginners" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
                            </div>
                            
                            <div class="mb-4">
                                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select id="kategori" name="category_id" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="harga" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                                <input type="number" id="harga" name="price" value="{{ old('price', $course->price) }}" placeholder="Contoh: 350000" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('price') border-red-500 @enderror">
                            </div>

                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status Publikasi</label>
                                <select id="status" name="status" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror">
                                    <option value="draft" {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft (Belum Dipublikasi)</option>
                                    <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Aktif (Dipublikasi)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            {{-- THUMBNAIL PREVIEW --}}
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail Kursus Saat Ini</label>
                                
                                <div id="thumbnail-preview-container">
                                    @if ($course->thumbnail_path)
                                        <img id="current-thumbnail" src="{{ asset('storage/' . $course->thumbnail_path) }}" alt="Thumbnail {{ $course->title }}" class="w-full h-40 object-cover rounded-lg mb-3 shadow-md border border-gray-200">
                                    @else
                                        <div id="current-thumbnail" class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 mb-3 border border-dashed border-gray-400">
                                            Tidak ada gambar
                                        </div>
                                    @endif
                                </div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Thumbnail</label>
                                <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition @error('thumbnail') border-red-500 @enderror">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4V28" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload-thumbnail" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Pilih file baru</span>
                                                <input id="file-upload-thumbnail" name="thumbnail" type="file" class="sr-only"> 
                                            </label>
                                            <p class="pl-1">atau tarik dan lepas</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- SYLLABUS FILE STATUS & UPLOAD --}}
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">File Silabus (PDF/DOCX)</label>

                                <div id="syllabus-status-container" class="mb-2">
                                    @if ($course->syllabus_path)
                                        <p class="text-xs text-gray-600">File saat ini: <a id="current-syllabus-link" href="{{ asset('storage/' . $course->syllabus_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">{{ basename($course->syllabus_path) }}</a></p>
                                    @else
                                        <p class="text-xs text-gray-500">Belum ada file silabus.</p>
                                    @endif
                                </div>

                                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti File Silabus</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition @error('syllabus') border-red-500 @enderror">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file-upload-syllabus" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Pilih file baru</span>
                                                <input id="file-upload-syllabus" name="syllabus" type="file" class="sr-only"> 
                                            </label>
                                            <p class="pl-1">atau tarik dan lepas</p>
                                        </div>
                                        <p id="syllabus-filename" class="text-xs text-gray-500">PDF, DOC, DOCX hingga 10MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Deskripsi dan Konten Kursus</h2>

                    <div class="mb-6">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (max 160 karakter)</label>
                        <textarea id="deskripsi" name="short_description" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('short_description') border-red-500 @enderror" placeholder="Ringkasan singkat kursus...">{{ old('short_description', $course->short_description) }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="konten" class="block text-sm font-medium text-gray-700 mb-1">Struktur & Isi Kursus (Text Editor)</label>
                        
                        <div class="border border-gray-300 rounded-lg overflow-hidden @error('content') border-red-500 @enderror">
                            <div class="flex bg-gray-100 p-2 space-x-2 border-b">
                                <span class="p-1 text-gray-600 hover:bg-gray-200 rounded cursor-pointer font-bold">B</span>
                                <span class="p-1 text-gray-600 hover:bg-gray-200 rounded cursor-pointer italic">I</span>
                                <span class="p-1 text-gray-600 hover:bg-gray-200 rounded cursor-pointer underline">U</span>
                                <span class="p-1 text-gray-600 hover:bg-gray-200 rounded cursor-pointer">H2</span>
                                <span class="p-1 text-gray-600 hover:bg-gray-200 rounded cursor-pointer">List</span>
                            </div>
                            <textarea id="konten" name="content" rows="10" class="block w-full p-4 focus:ring-0 focus:border-transparent border-none resize-none" placeholder="Masukkan struktur kursus, modul, dan poin-poin penting di sini... (Area Text Editor)">{{ old('content', $course->content) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.courses.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-indigo-700 transition">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const thumbnailInput = document.getElementById('file-upload-thumbnail');
        const syllabusInput = document.getElementById('file-upload-syllabus');
        const previewContainer = document.getElementById('thumbnail-preview-container');
        const syllabusStatusContainer = document.getElementById('syllabus-status-container');
        const syllabusFilenameText = document.getElementById('syllabus-filename');

        // --- 1. Live Preview Thumbnail ---
        if (thumbnailInput) {
            thumbnailInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Ganti konten preview container dengan tag <img> baru
                        previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" class="w-full h-40 object-cover rounded-lg mb-3 shadow-md border border-gray-200">`;
                    }
                    reader.readAsDataURL(file);
                } else {
                    // Jika file dibatalkan, kembalikan ke thumbnail lama atau placeholder
                    previewContainer.innerHTML = `@if ($course->thumbnail_path)
                        <img id="current-thumbnail" src="{{ asset('storage/' . $course->thumbnail_path) }}" alt="Thumbnail {{ $course->title }}" class="w-full h-40 object-cover rounded-lg mb-3 shadow-md border border-gray-200">
                    @else
                        <div id="current-thumbnail" class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 mb-3 border border-dashed border-gray-400">
                            Tidak ada gambar
                        </div>
                    @endif`;
                }
            });
        }

        // --- 2. Tampilkan Nama File Silabus yang Baru Di-upload ---
        if (syllabusInput) {
            syllabusInput.addEventListener('change', function(event) {
                const file = event.target.files[0];
                const originalText = 'PDF, DOC, DOCX hingga 10MB';

                if (file) {
                    // Update teks status saat file baru dipilih
                    syllabusStatusContainer.innerHTML = `<p class="text-xs text-green-600">File Baru Dipilih: **${file.name}**</p>`;
                    // Update teks panduan di bawah kotak upload
                    syllabusFilenameText.textContent = `File akan menggantikan yang lama.`;
                } else {
                    // Reset status dan teks jika input dibatalkan
                    syllabusStatusContainer.innerHTML = `@if ($course->syllabus_path)
                        <p class="text-xs text-gray-600">File saat ini: <a id="current-syllabus-link" href="{{ asset('storage/' . $course->syllabus_path) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800 underline">{{ basename($course->syllabus_path) }}</a></p>
                    @else
                        <p class="text-xs text-gray-500">Belum ada file silabus.</p>
                    @endif`;
                    syllabusFilenameText.textContent = originalText;
                }
            });
        }
    });

    // Kode Navigasi yang sudah ada (tidak diubah)
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    
    const kursusDropdownButtonMobile = document.getElementById('mobile-dropdown-kursus-button');
    const kursusMenuMobile = document.getElementById('mobile-kursus-menu');
    const kursusIconMobile = document.getElementById('mobile-kursus-icon');

    const kursusDropdownDesktop = document.getElementById('kursus-dropdown-desktop');
    const profileDropdownDesktop = document.getElementById('profile-dropdown-desktop');

    function closeAllDropdowns() {
        if(kursusDropdownDesktop) kursusDropdownDesktop.classList.remove('dropdown-open');
        if(profileDropdownDesktop) profileDropdownDesktop.classList.remove('dropdown-open');
    }

    if (mobileMenuButton) {
        mobileMenuButton.addEventListener('click', function() {
            closeAllDropdowns();
            const isOpen = mobileMenu.classList.toggle('open');
            if (isOpen) {
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
            } else {
                menuIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
            }
        });
    }

    if(kursusDropdownButtonMobile && kursusMenuMobile && kursusIconMobile) {
        kursusDropdownButtonMobile.addEventListener('click', function() {
            const isKursusOpen = kursusMenuMobile.style.display === 'block';

            if (isKursusOpen) {
                kursusMenuMobile.style.display = 'none';
                kursusIconMobile.classList.remove('rotate-180');
            } else {
                kursusMenuMobile.style.display = 'block';
                kursusIconMobile.classList.add('rotate-180');
            }
        });
    }

    if(kursusDropdownDesktop && document.getElementById('kursus-dropdown-button')) {
        document.getElementById('kursus-dropdown-button').addEventListener('click', function(event) {
            event.stopPropagation();
            if(profileDropdownDesktop) profileDropdownDesktop.classList.remove('dropdown-open');
            kursusDropdownDesktop.classList.toggle('dropdown-open');
        });
    }

    if(profileDropdownDesktop && document.getElementById('profile-dropdown-button')) {
        document.getElementById('profile-dropdown-button').addEventListener('click', function(event) {
            event.stopPropagation();
            if(kursusDropdownDesktop) kursusDropdownDesktop.classList.remove('dropdown-open');
            profileDropdownDesktop.classList.toggle('dropdown-open');
        });
    }

    document.addEventListener('click', function(event) {
        if (kursusDropdownDesktop && !kursusDropdownDesktop.contains(event.target) && 
            profileDropdownDesktop && !profileDropdownDesktop.contains(event.target)) {
            closeAllDropdowns();
        }
    });
</script>   
@endpush