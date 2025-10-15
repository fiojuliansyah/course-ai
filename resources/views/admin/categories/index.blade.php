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
                <h1 class="text-3xl font-extrabold text-gray-900">Daftar Kategori</h1>
                
                <a href="{{ route('admin.categories.create') }}" class="flex items-center bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium shadow-md hover:bg-indigo-700 transition duration-150">
                    <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Tambah Kategori
                </a>
            </div>

            <div class="bg-white shadow-xl rounded-xl border border-gray-100 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Slug</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Jumlah Kursus</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $category->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    {{ $category->slug }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                                    {{ $category->courses_count ?? 0 }} 
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 transition">Edit</a>
                                    
                                    <button class="delete-category-btn text-red-600 hover:text-red-900 transition" 
                                        data-category-id="{{ $category->id }}" 
                                        data-category-name="{{ $category->name }}">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">Belum ada kategori yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    {{-- MODAL KONFIRMASI DELETE - PERBAIKAN STYLING MODAL --}}
<div id="delete-category-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
    
    {{-- PERUBAHAN DI SINI: Hilangkan top-20 dan tambahkan my-8 dan max-h-full --}}
    <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white my-8 max-h-full overflow-y-auto">
        
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.39 16c-.77 1.333.192 3 1.732 3z" /></svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-3">Hapus Kategori</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Anda akan menghapus kategori: **<span id="modal-category-name" class="font-semibold text-gray-700"></span>**. Kursus yang terkait akan diset ke 'Tanpa Kategori'. Lanjutkan?
                </p>
            </div>
            <div class="items-center px-4 py-3 sm:flex sm:flex-row-reverse">
                
                <form id="delete-category-form" method="POST" class="sm:ml-3 sm:text-sm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Ya, Hapus
                    </button>
                </form>

                <button type="button" id="cancel-category-delete-btn" class="mt-3 w-full sm:mt-0 sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        // INISIALISASI MODAL DELETE KATEGORI
        const deleteCategoryModal = document.getElementById('delete-category-modal');
        const cancelCategoryDeleteBtn = document.getElementById('cancel-category-delete-btn');
        const deleteCategoryButtons = document.querySelectorAll('.delete-category-btn');
        const modalCategoryName = document.getElementById('modal-category-name');
        const deleteCategoryForm = document.getElementById('delete-category-form');
        
        const categoryDestroyBaseUrl = '{{ url('admin/categories') }}'; 

        // 1. Tampilkan Modal
        deleteCategoryButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const categoryId = this.getAttribute('data-category-id');
                const categoryName = this.getAttribute('data-category-name');
                
                modalCategoryName.textContent = categoryName;
                deleteCategoryForm.action = `${categoryDestroyBaseUrl}/${categoryId}`;
                
                // Tampilkan: Hapus 'hidden' dan tambahkan 'flex'
                deleteCategoryModal.classList.remove('hidden');
                deleteCategoryModal.classList.add('flex'); 
            });
        });

        // 2. Sembunyikan Modal
        cancelCategoryDeleteBtn.addEventListener('click', function() {
            deleteCategoryModal.classList.remove('flex');
            deleteCategoryModal.classList.add('hidden');
        });

        // 3. Sembunyikan Modal saat klik backdrop
        deleteCategoryModal.addEventListener('click', function(e) {
            if (e.target === deleteCategoryModal) {
                deleteCategoryModal.classList.remove('flex');
                deleteCategoryModal.classList.add('hidden');
            }
        });

    </script>
@endpush