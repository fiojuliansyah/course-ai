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
                <strong class="font-bold">Error validasi!</strong>
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Pengaturan Website</h1>
            
            <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8" enctype="multipart/form-data">
                @csrf 
                @method('PUT')
                
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100 max-w-3xl">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Informasi Dasar</h2>
                    
                    {{-- Nama Website --}}
                    <div class="mb-4">
                        <label for="site_name" class="block text-sm font-medium text-gray-700">Nama Website</label>
                        <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('site_name') border-red-500 @enderror">
                        @error('site_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email Kontak --}}
                    <div class="mb-4">
                        <label for="site_email" class="block text-sm font-medium text-gray-700">Email Kontak</label>
                        <input type="email" id="site_email" name="site_email" value="{{ old('site_email', $settings['site_email'] ?? '') }}" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('site_email') border-red-500 @enderror">
                        @error('site_email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-4">
                        <label for="site_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                        <textarea id="site_address" name="site_address" rows="3" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('site_address') border-red-500 @enderror">{{ old('site_address', $settings['site_address'] ?? '') }}</textarea>
                        @error('site_address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    
                    {{-- Logo --}}
                    <div class="mb-4">
                        <label for="site_logo" class="block text-sm font-medium text-gray-700">Logo Website</label>
                        @if (isset($settings['site_logo']) && $settings['site_logo'])
                            <p class="text-sm text-gray-500 mb-2">Logo Saat Ini:</p>
                            <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Logo Saat Ini" class="mb-3 h-12 w-auto border p-1 rounded">
                        @endif
                        <input type="file" id="site_logo" name="site_logo" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('site_logo') border-red-500 @enderror">
                        @error('site_logo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 max-w-3xl">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-indigo-700 transition">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

        </div>
    </main>
@endsection