@extends('student.layouts.app')

@section('content')
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">

            <h1 class="text-3xl font-extrabold text-gray-900 mb-4">Selamat Datang, {{ Auth::user()->name ?? 'Siswa' }}!</h1>
            <p class="text-gray-500 mb-8">Ikhtisar progres dan kursus Anda.</p>
            
            {{-- GRID STATISTIK UTAMA (TIDAK BERUBAH) --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-10">
                
                {{-- Card 1: Kursus Aktif (Statik) --}}
                <div class="bg-white shadow-xl rounded-xl p-5 border border-indigo-100 transition duration-300 hover:shadow-2xl">
                    <p class="text-sm font-medium text-indigo-600 truncate">Kursus Aktif</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">3</p>
                        <p class="ml-2 text-sm font-medium text-gray-500">dari 5 total</p>
                    </div>
                </div>
                
                {{-- Card 2: Materi Selesai (Statik) --}}
                <div class="bg-white shadow-xl rounded-xl p-5 border border-gray-100 transition duration-300 hover:shadow-2xl">
                    <p class="text-sm font-medium text-gray-500 truncate">Materi Selesai</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">72%</p>
                        <p class="ml-2 text-sm font-medium text-green-600">Terus tingkatkan!</p>
                    </div>
                </div>

                {{-- Card 3: Sertifikat Diraih (Statik) --}}
                <div class="bg-white shadow-xl rounded-xl p-5 border border-gray-100 transition duration-300 hover:shadow-2xl">
                    <p class="text-sm font-medium text-gray-500 truncate">Sertifikat Diraih</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">1</p>
                        <p class="ml-2 text-sm font-medium text-gray-500">pencapaian</p>
                    </div>
                </div>

                {{-- Card 4: Kuis Terakhir (Statik) --}}
                <div class="bg-white shadow-xl rounded-xl p-5 border border-gray-100 transition duration-300 hover:shadow-2xl">
                    <p class="text-sm font-medium text-gray-500 truncate">Nilai Kuis Terakhir</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">95</p>
                        <p class="ml-2 text-sm font-medium text-blue-600">Skor Terbaik</p>
                    </div>
                </div>
            </div>
            
            {{-- DUA KOLOM: KURSUS SAYA DAN KURSUS TERBARU --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- KOLOM KIRI (Kursus Saya - TIDAK BERUBAH) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Kursus Anda</h2>
                        
                        <div class="space-y-6">
                            {{-- LOOP KURSUS AKTIF (Simulasi) --}}
                            @for ($i = 1; $i <= 3; $i++) 
                            @php 
                                $progressValue = [25, 75, 100][($i - 1) % 3];
                                $isCompleted = $progressValue == 100;
                            @endphp
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center p-4 border border-gray-200 rounded-lg transition hover:bg-indigo-50/50">
                                    <div class="flex-1 mb-3 md:mb-0">
                                        <p class="text-lg font-semibold text-gray-800">
                                            {{ $i }}. Nama Kursus Dinamis {{ $i }}
                                        </p>
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                            <div class="h-2.5 rounded-full {{ $isCompleted ? 'bg-green-500' : 'bg-indigo-600' }}" style="width: {{ $progressValue }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 mt-1">{{ $progressValue }}% Selesai</span>
                                    </div>
                                    <div class="ml-0 md:ml-6 flex space-x-3">
                                        @if ($isCompleted)
                                            <span class="px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full">SELESAI</span>
                                            <button class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-700">Lihat Sertifikat</button>
                                        @else
                                            <button class="bg-indigo-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-indigo-700">Lanjutkan Belajar</button>
                                        @endif
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (Kursus Terbaru - MODIFIKASI) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100 sticky top-20">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Kursus Terbaru 🔥</h2>
                        
                        <div class="space-y-4">
                            @forelse ($latestCourses as $course)
                                {{-- Card Kursus Terbaru --}}
                                <div class="border border-gray-200 rounded-lg overflow-hidden transition hover:shadow-md">
                                    {{-- Gambar Kursus --}}
                                    <img class="w-full h-24 object-cover" 
                                         src="{{ $course->thumbnail_path ? asset('storage/' . $course->thumbnail_path) : 'https://via.placeholder.com/600x400/9333ea/ffffff?text=E-Course' }}" 
                                         alt="Gambar Kursus {{ $course->title }}">

                                    <div class="p-3">
                                        <p class="text-sm font-semibold text-gray-800">{{ $course->title }}</p>
                                        <p class="text-xs text-gray-500 mt-1 mb-2 line-clamp-2">{{ Str::limit($course->short_description, 60) }}</p>
                                        
                                        {{-- Harga --}}
                                        @if ($course->price > 0)
                                            <p class="text-lg font-bold text-green-600 mt-2">
                                                {{ 'Rp' . number_format($course->price, 0, ',', '.') }}
                                            </p>
                                        @else
                                            <p class="text-lg font-bold text-indigo-600 mt-2">
                                                Gratis
                                            </p>
                                        @endif
                                        
                                        {{-- Tombol Enroll --}}
                                        <a href="#" {{-- Ganti # dengan route enroll kursus: route('student.courses.show', $course) --}}
                                           class="mt-3 block w-full text-center bg-indigo-600 text-white text-sm py-1.5 rounded-lg hover:bg-indigo-700 transition">
                                            Lihat & Enroll
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">Tidak ada kursus baru saat ini.</p>
                            @endforelse
                        </div>
                        
                        <div class="mt-4 pt-4 border-t text-right">
                             <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Lihat Semua Kursus &rarr;</a>
                        </div>
                    </div>
                </div>

            </div>
            
            {{-- AREA PENCAPAIAN/SERTIFIKAT (TIDAK BERUBAH) --}}
            <div class="mt-8 bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Pencapaian & Riwayat Kuis</h2>
                <p class="text-gray-500 text-sm italic">Area ini menampilkan riwayat nilai kuis dan sertifikat yang telah Anda peroleh.</p>
                
                <div class="mt-4 p-4 border rounded-lg bg-gray-50">
                    <p class="font-semibold">Riwayat Kuis Terakhir:</p>
                    <ul class="list-disc list-inside text-sm text-gray-700 mt-2">
                        <li>Quiz Modul 3: **88/100** (Lulus)</li>
                        <li>Quiz Modul 2: **95/100** (Lulus)</li>
                    </ul>
                </div>
            </div>

        </div>
    </main>
@endsection