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

                <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Progres Belajar</h2>
                    <div class="text-center my-6">
                        <div class="w-24 h-24 mx-auto border-4 border-indigo-200 rounded-full flex items-center justify-center text-indigo-600 text-xl font-bold">
                            45%
                        </div>
                        <p class="mt-3 text-gray-600">Total Progres Selesai</p>
                    </div>
                    <div class="space-y-2 mt-4 border-t pt-4">
                        <button class="w-full bg-blue-100 text-blue-700 py-2 rounded-lg text-sm hover:bg-blue-200">Lihat Riwayat Kuis</button>
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
                                                data-content="{{ $material->content }}">
                                                Lihat Konten
                                            </button>
                                        @endif

                                        @if ($material->file_path)
                                            <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="text-sm text-green-600 hover:text-green-800">
                                                Download
                                            </a>
                                        @endif

                                        {{-- Add Selesai Membaca button --}}
                                        <button type="button" class="finish-reading-btn text-sm text-white bg-indigo-600 hover:bg-indigo-700 px-4 py-2 rounded-md"
                                                data-material-id="{{ $material->id }}">
                                            Selesai Membaca
                                        </button>
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
        const finishReadingButtons = document.querySelectorAll('.finish-reading-btn');

        // Function to toggle modal visibility
        function toggleModal(modalId, show) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            modal.classList.toggle('hidden', !show);
            modal.classList.toggle('flex', show);
        }

        // Tab navigation
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                tabButtons.forEach(btn => btn.classList.remove('bg-indigo-600', 'text-white', 'hover:bg-indigo-700'));
                tabContentItems.forEach(content => content.classList.add('hidden'));

                this.classList.add('bg-indigo-600', 'text-white', 'hover:bg-indigo-700');
                const targetContent = document.getElementById(targetId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                }
            });
        });

        // Set first tab as active by default
        if (tabButtons.length > 0) {
            tabButtons[0].click();
        }

        // View content modal logic
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

        // Finish reading button functionality
        finishReadingButtons.forEach(button => {
            button.addEventListener('click', function() {
                const materialId = this.getAttribute('data-material-id');

                fetch(`/student/material/${materialId}/complete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ material_id: materialId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        this.innerText = 'SELESAI';
                        this.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error marking material as completed:', error);
                });
            });
        });
    });
</script>
@endpush
