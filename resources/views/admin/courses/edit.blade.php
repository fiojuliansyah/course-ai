@extends('admin.layouts.app')

@section('content')
    {{-- Notifikasi Error Validasi --}}
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
            
            {{-- FORMULIR UTAMA: Mengarah ke CourseController@update --}}
            {{-- Perlu @method('PUT') dan enctype="multipart/form-data" --}}
            <form action="{{ route('admin.courses.update', $course) }}" method="POST" class="space-y-8" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                
                {{-- BAGIAN 1: DETAIL DASAR --}}
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Detail Dasar Kursus</h2>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Kolom Kiri --}}
                        <div>
                            <div class="mb-4">
                                <label for="judul" class="block text-sm font-medium text-gray-700">Judul Kursus</label>
                                {{-- Memuat data lama atau data dari objek $course --}}
                                <input type="text" id="judul" name="title" value="{{ old('title', $course->title) }}" placeholder="Contoh: Tailwind CSS for Beginners" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('title') border-red-500 @enderror">
                            </div>
                            
                            <div class="mb-4">
                                <label for="kategori" class="block text-sm font-medium text-gray-700">Kategori</label>
                                <select id="kategori" name="category_id" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror">
                                    <option value="">Pilih Kategori</option>
                                    {{-- LOOPING DATA KATEGORI DINAMIS --}}
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{-- Logika selected: jika old('category_id') cocok, atau category_id dari DB cocok --}}
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

                        {{-- Kolom Kanan (Thumbnail) --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail Kursus Saat Ini</label>
                            
                            {{-- Preview Gambar Saat Ini --}}
                            @if ($course->thumbnail_path)
                                <img src="{{ asset('storage/' . $course->thumbnail_path) }}" alt="Thumbnail {{ $course->title }}" class="w-full h-40 object-cover rounded-lg mb-3 shadow-md border border-gray-200">
                            @else
                                <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500 mb-3 border border-dashed border-gray-400">
                                    Tidak ada gambar
                                </div>
                            @endif

                            {{-- Upload Pengganti --}}
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Thumbnail</label>
                            <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-indigo-400 transition @error('thumbnail') border-red-500 @enderror">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m-4-4V28" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Pilih file baru</span>
                                            <input id="file-upload" name="thumbnail" type="file" class="sr-only"> 
                                        </label>
                                        <p class="pl-1">atau tarik dan lepas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 2: DESKRIPSI DAN KONTEN --}}
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Deskripsi dan Konten Kursus</h2>

                    <div class="mb-6">
                        <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (max 160 karakter)</label>
                        <textarea id="deskripsi" name="short_description" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('short_description') border-red-500 @enderror" placeholder="Ringkasan singkat kursus...">{{ old('short_description', $course->short_description) }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="konten" class="block text-sm font-medium text-gray-700 mb-1">Struktur & Isi Kursus (Text Editor)</label>
                        
                        {{-- Placeholder Text Editor --}}
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

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.courses.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-indigo-700 transition">Simpan Perubahan</button>
                </div>
            </form>

        </div>
    </main>
@endsection