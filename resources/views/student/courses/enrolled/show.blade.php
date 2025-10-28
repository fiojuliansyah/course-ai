@extends('student.layouts.app')

@section('content')
<main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('student.courses.enrolled.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Kembali ke Kursus Saya</a>
            <span class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded-full">Status: AKTIF</span>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 mb-4">{{ $course->title }}</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            {{-- KOLOM KIRI (NAVIGASI TAB MODUL) --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white shadow-xl rounded-xl border border-gray-100 p-4 sticky top-20">
                    <h2 class="text-xl font-bold text-gray-800 mb-3 border-b pb-2">Daftar Modul</h2>

                    <nav class="flex flex-col space-y-1" id="module-tabs">
                        @forelse ($course->modules as $module)
                            <button type="button" 
                                    class="tab-button w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition duration-150 
                                        @if ($loop->first) bg-indigo-600 text-white hover:bg-indigo-700 @else text-gray-700 hover:bg-indigo-50 @endif"
                                    data-target="tab-{{ $module->id }}">
                                {{ $module->order }}. {{ $module->title }}
                            </button>
                        @empty
                            <p class="text-gray-500 text-sm">Silabus belum tersedia.</p>
                        @endforelse
                    </nav>
                </div>

                {{-- KOLOM KANAN KECIL: PROGRESS & QUIZ BUTTON --}}
                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Progres Belajar</h2>
                    <div class="text-center my-6">
                        <div class="w-24 h-24 mx-auto border-4 border-indigo-200 rounded-full flex items-center justify-center text-indigo-600 text-xl font-bold">
                            0%
                        </div>
                        <p class="mt-3 text-gray-600">Total Progres Selesai</p>
                    </div>
                    <div class="space-y-2 mt-4 border-t pt-4">
                        
                        {{-- TOMBOL BARU: LINK KE KUIS --}}
                        <a href="{{ route('student.courses.quiz.show', $course) }}" class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700 font-semibold flex items-center justify-center transition">
                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            Mulai Kuis Akhir
                        </a>
                        
                        <button class="w-full bg-yellow-100 text-yellow-700 py-2 rounded-lg text-sm hover:bg-yellow-200">Kirim Feedback</button>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN (KONTEN MODUL AKTIF) --}}
            <div class="lg:col-span-3 space-y-6">
                @forelse ($course->modules as $module)
                    <div id="tab-{{ $module->id }}" 
                         class="tab-content-item bg-white shadow-xl rounded-xl p-6 border border-gray-100 @if (!$loop->first) hidden @endif">
                        <h2 class="text-2xl font-bold text-indigo-600 mb-4 border-b pb-2">{{ $module->title }}</h2>

                        <ul class="divide-y divide-gray-100 space-y-1">
                            @forelse ($module->materials as $material)
                                <li class="flex justify-between items-center py-3">
                                    <span class="text-sm text-gray-700 font-semibold">
                                        {{ $module->order }}.{{ $material->order }}. {{ $material->title }}
                                    </span>

                                    <div class="space-x-2 flex items-center">
                                        @if ($material->content)
                                            <button type="button" class="view-content-btn text-sm text-indigo-600 hover:text-indigo-800"
                                                data-title="{{ $material->title }}"
                                                data-content="{{ $material->content }}"
                                                data-material-id="{{ $material->id }}">
                                                Lihat Konten
                                            </button>
                                        @endif

                                        @if ($material->file_path)
                                            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-sm text-green-600 hover:text-green-800">
                                                Download
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="p-4 text-gray-500 italic text-sm">Tidak ada materi di modul ini.</li>
                            @endforelse
                        </ul>

                        <div class="mt-4 pt-4 border-t text-right">
                            <span class="text-sm text-gray-500 italic">{{ $module->description }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 p-5">Silabus belum tersedia.</div>
                @endforelse
            </div>
        </div>
    </div>
</main>

{{-- MODAL UNTUK MENAMPILKAN KONTEN DOKUMEN --}}
<div id="content-modal" class="modal fixed inset-0 bg-gray-600 bg-opacity-75 w-full z-50 hidden items-center justify-center">
    <div class="relative mx-auto p-8 border w-11/12 md:w-3/5 lg:w-1/2 shadow-2xl rounded-xl bg-white max-h-[90vh] overflow-y-auto">
        <h3 id="content-modal-title" class="text-xl font-extrabold text-gray-900 mb-4 border-b pb-2"></h3>
        <div id="content-modal-body" class="prose max-w-none text-gray-700"></div>
        <div class="mt-6 flex justify-end">
            <button type="button" id="close-content-modal" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition">Tutup</button>
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
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContentItems = document.querySelectorAll('.tab-content-item');
        const contentModal = document.getElementById('content-modal');
        const contentModalTitle = document.getElementById('content-modal-title');
        const contentModalBody = document.getElementById('content-modal-body');
        const closeContentModalBtn = document.getElementById('close-content-modal');
        const viewContentButtons = document.querySelectorAll('.view-content-btn');
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function toggleModal(modalId, show) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.toggle('hidden', !show);
            modal.classList.toggle('flex', show);
        }
        
        function markMaterialAsCompleted(materialId) {
            fetch(`{{ url('student/material') }}/${materialId}/complete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update status visual
                    const statusSpan = document.getElementById(`status-${materialId}`);
                    if (statusSpan) {
                        statusSpan.textContent = 'SELESAI';
                        statusSpan.classList.remove('text-gray-400');
                        statusSpan.classList.add('text-green-500');
                    }
                }
            })
            .catch(error => {
                console.error('Network or server error:', error);
            });
        }

        // 1. Logic Tab Navigation
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                
                // Non-aktifkan semua tombol dan sembunyikan semua konten
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                    btn.classList.add('text-gray-700', 'hover:bg-indigo-50');
                });
                tabContentItems.forEach(content => {
                    content.classList.add('hidden');
                });
                
                // Aktifkan tombol yang diklik dan tampilkan konten target
                this.classList.add('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                this.classList.remove('text-gray-700', 'hover:bg-indigo-50');
                
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });
        
        // Set Tab pertama aktif secara default (jika ada)
        if (tabButtons.length > 0) {
            tabButtons[0].click(); 
        }


        // 2. Logic Menampilkan Modal Konten Dokumen & Mark Complete
        viewContentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const title = this.getAttribute('data-title');
                const contentMarkdown = this.getAttribute('data-content');
                const materialId = this.getAttribute('data-material-id');

                // 1. TANDAI SELESAI VIA AJAX
                if (materialId) {
                    markMaterialAsCompleted(materialId);
                }
                
                // 2. TAMPILKAN MODAL
                contentModalTitle.textContent = title;
                contentModalBody.innerHTML = marked.parse(contentMarkdown); 
                
                toggleModal('content-modal', true);
            });
        });
        
        closeContentModalBtn.addEventListener('click', () => toggleModal('content-modal', false));
        contentModal.addEventListener('click', (e) => {
            if (e.target === contentModal) toggleModal('content-modal', false);
        });
    });
</script>
@endpush