@extends('admin.layouts.app')

@section('content')
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Gagal menyimpan perubahan!</strong>
                <span class="block sm:inline">Tolong periksa kembali input Anda.</span>
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Edit Kategori: {{ $category->name }}</h1>
            
            {{-- FORMULIR EDIT: Mengarah ke CategoryController@update --}}
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT') {{-- PENTING: Untuk method UPDATE --}}
                
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100 max-w-2xl">
                    
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Kategori</label>
                        <input type="text" id="name" name="name" 
                            value="{{ old('name', $category->name) }}" {{-- Memuat nilai lama atau nilai dari DB --}}
                            placeholder="Contoh: Pemrograman Web" 
                            class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Singkat (Opsional)</label>
                        <textarea id="description" name="description" rows="3" 
                            class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror" 
                            placeholder="Jelaskan fokus utama kategori ini...">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 max-w-2xl">
                    <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-indigo-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </main>
@endsection
{{-- @push('scripts') dihilangkan karena tidak ada skrip spesifik di form edit --}}