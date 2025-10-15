@extends('admin.layouts.app')

@section('content')
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900">Daftar Pengguna</h1>
                
                {{-- Tombol Tambah Pengguna Baru (Opsional) --}}
                <a href="{{ route('admin.users.create') }}" class="flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-indigo-700 transition duration-150">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Pengguna
                </a>
            </div>

            {{-- Filter dan Search Bar --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 space-y-4 sm:space-y-0 sm:space-x-4">
                <input type="text" placeholder="Cari nama atau email..." class="w-full sm:w-1/3 p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                
                <div class="flex w-full sm:w-auto space-x-4">
                    <select class="p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option>Semua Peran</option>
                        <option>Admin</option>
                        <option>Instruktur</option>
                        <option>Siswa</option>
                    </select>
                </div>
            </div>

            {{-- Tabel Daftar Pengguna --}}
            <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Peran</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Bergabung Sejak</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Kursus Diambil</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr>
                                {{-- Kolom Pengguna (Nama & Email) --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            {{-- Ganti dengan avatar asli jika ada --}}
                                            <img class="h-10 w-10 rounded-full border border-gray-300" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E0E7FF&color=4F46E5&bold=true" alt="{{ $user->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                
                                {{-- Kolom Peran --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 hidden md:table-cell">
                                    {{-- Asumsi $user memiliki atribut 'role' --}}
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if ($user->role === 'admin') bg-indigo-100 text-indigo-800
                                        @elseif ($user->role === 'instructor') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                
                                {{-- Kolom Bergabung Sejak --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    {{ $user->created_at->diffForHumans() }}
                                </td>

                                {{-- Kolom Kursus Diambil --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                    {{-- Asumsi $user memiliki atribut 'courses_taken' --}}
                                    {{ $user->courses_taken ?? '0' }}
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 transition">Edit</a>
                                    
                                    <button class="delete-user-btn text-red-600 hover:text-red-900 transition" 
                                        data-user-id="{{ $user->id }}" 
                                        data-user-name="{{ $user->name }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Tidak ada pengguna yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginasi --}}
            <div class="mt-8 flex justify-center">
                {{ $users->links('pagination::tailwind') }}
            </div>

        </div>
    </main>

    {{-- MODAL KONFIRMASI DELETE (Harus berada di luar @section('content')) --}}
    <div id="delete-user-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white max-h-full overflow-y-auto">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Konfirmasi Hapus Pengguna</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Apakah Anda yakin ingin menghapus pengguna: **<span id="modal-user-name" class="font-semibold text-gray-700"></span>**? Tindakan ini tidak dapat dibatalkan.
                    </p>
                </div>
                <div class="items-center px-4 py-3 sm:flex sm:flex-row-reverse">
                    
                    <form id="delete-user-form" method="POST" class="sm:ml-3 sm:text-sm">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Hapus Permanen
                        </button>
                    </form>

                    <button type="button" id="cancel-user-delete-btn" class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // INISIALISASI MODAL DELETE PENGGUNA
        const deleteUserModal = document.getElementById('delete-user-modal');
        const cancelUserDeleteBtn = document.getElementById('cancel-user-delete-btn');
        const deleteUserButtons = document.querySelectorAll('.delete-user-btn');
        const modalUserName = document.getElementById('modal-user-name');
        const deleteUserForm = document.getElementById('delete-user-form');
        
        // Asumsi base URL untuk pengguna adalah admin/users
        const userDestroyBaseUrl = '{{ url('admin/users') }}'; 

        // 1. Tampilkan Modal
        deleteUserButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                modalUserName.textContent = userName;
                deleteUserForm.action = `${userDestroyBaseUrl}/${userId}`;
                
                deleteUserModal.classList.remove('hidden');
                deleteUserModal.classList.add('flex'); 
            });
        });

        // 2. Sembunyikan Modal
        cancelUserDeleteBtn.addEventListener('click', function() {
            deleteUserModal.classList.remove('flex');
            deleteUserModal.classList.add('hidden');
        });

        // 3. Sembunyikan Modal saat klik backdrop
        deleteUserModal.addEventListener('click', function(e) {
            if (e.target === deleteUserModal) {
                deleteUserModal.classList.remove('flex');
                deleteUserModal.classList.add('hidden');
            }
        });
    </script>
    {{-- Pastikan script Navigasi ada di layout induk --}}
@endpush