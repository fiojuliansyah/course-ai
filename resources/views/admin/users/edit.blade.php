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
            
            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Edit Pengguna: {{ $user->name }}</h1>
            
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100 max-w-3xl">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Detail Akun</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Nama Lengkap --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" id="name" name="name" 
                                value="{{ old('name', $user->name) }}" 
                                placeholder="Nama Lengkap" 
                                class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror">
                            @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        {{-- Alamat Email --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" 
                                value="{{ old('email', $user->email) }}" 
                                placeholder="Email Pengguna" 
                                class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <p class="md:col-span-2 text-sm text-gray-500 italic border-b pb-2">Kosongkan kolom password di bawah jika Anda tidak ingin mengubahnya.</p>
                        
                        {{-- Password Baru --}}
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                            <input type="password" id="password" name="password" placeholder="Biarkan kosong jika tidak diubah" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('password') border-red-500 @enderror">
                            @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        {{-- Konfirmasi Password --}}
                        <div class="mb-4">
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Ulangi password baru" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        {{-- Peran (Role) --}}
                        <div class="mb-4 md:col-span-2">
                            <label for="role" class="block text-sm font-medium text-gray-700">Peran Akun</label>
                            <select id="role" name="role" class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('role') border-red-500 @enderror">
                                <option value="">Pilih Peran</option>
                                {{-- Menggunakan $roles dari UserController@edit --}}
                                @foreach (['siswa', 'instructor', 'admin'] as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200 max-w-3xl">
                    <a href="{{ route('admin.users.index') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg font-semibold hover:bg-gray-300 transition">Batal</a>
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold shadow-md hover:bg-indigo-700 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </main>
@endsection