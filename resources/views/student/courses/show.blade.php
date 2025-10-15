@extends('student.layouts.app')

@section('content')
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            
            {{-- Tambahkan Notifikasi (Jika ada redirect dari enroll) --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('student.courses.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Kembali ke Katalog</a>
            </div>
            
            {{-- Pastikan Anda memuat status enrollment di Student\CourseController@show --}}
            @php
                // ASUMSI MODIFIKASI: $currentEnrollment = $course->enrollments()->where('user_id', Auth::id())->first();
                // $isEnrolled = $currentEnrollment && $currentEnrollment->status === 'paid';
                // $isPending = $currentEnrollment && $currentEnrollment->status === 'pending';
                
                $price = $course->price ?? 0;
                $isEnrolled = false; // Placeholder, ganti dengan logic DB
                $isPending = false; // Placeholder, ganti dengan logic DB
            @endphp
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                
                {{-- KOLOM KIRI (Detail & Deskripsi) --}}
                <div class="lg:col-span-2 space-y-6">
                    <h1 class="text-4xl font-extrabold text-gray-900">{{ $course->title }}</h1>
                    <p class="text-gray-600 text-lg">{{ $course->short_description }}</p>

                    <div class="relative rounded-lg overflow-hidden shadow-md">
                        <img class="w-full h-80 object-cover" 
                             src="{{ $course->thumbnail_path ? asset('storage/' . $course->thumbnail_path) : 'https://via.placeholder.com/800x400?text=Course+Image' }}" 
                             alt="Gambar Kursus {{ $course->title }}">
                    </div>

                    <div class="pt-4">
                        <h2 class="text-2xl font-bold text-gray-800 mb-3 border-b pb-2">Deskripsi Lengkap</h2>
                        <div class="prose max-w-none text-gray-700">
                            {!! $course->content !!}
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN (Aksi & Silabus) --}}
                <div class="lg:col-span-1 space-y-6">
                    
                    {{-- KOTAK HARGA & ENROLL (MODIFIKASI) --}}
                    <div class="p-5 border border-indigo-300 rounded-xl bg-indigo-50 shadow-md">
                        
                        <p class="text-sm font-semibold text-indigo-600 mb-2">Harga Kursus:</p>

                        @if ($isEnrolled)
                            {{-- STATUS 1: SUDAH PAID --}}
                            <p class="text-2xl font-extrabold text-green-600 mb-4">Sudah Terdaftar!</p>
                            <a href="#" class="mt-4 block w-full text-center bg-green-600 text-white text-sm py-3 rounded-lg hover:bg-green-700 transition font-medium">
                                Lanjutkan Belajar &rarr;
                            </a>
                        @elseif ($isPending)
                            {{-- STATUS 2: PENDING PEMBAYARAN --}}
                            <p class="text-2xl font-extrabold text-yellow-600 mb-4">Pembayaran Tertunda</p>
                            <a href="{{ route('student.checkout', $course->enrollment) }}" {{-- Ganti dengan objek enrollment --}}
                               class="mt-4 block w-full text-center bg-yellow-600 text-white text-sm py-3 rounded-lg hover:bg-yellow-700 transition font-medium">
                                Lanjutkan Checkout
                            </a>
                        @else
                            {{-- STATUS 3: SIAP ENROLL --}}
                            @if ($price > 0)
                                <p class="text-3xl font-extrabold text-indigo-800">
                                    Rp{{ number_format($price, 0, ',', '.') }}
                                </p>
                            @else
                                <p class="text-3xl font-extrabold text-green-700">
                                    Gratis
                                </p>
                            @endif
                            
                            {{-- Tombol Enroll (Form POST) --}}
                            <form action="{{ route('student.enroll.start', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="mt-4 block w-full text-center bg-indigo-600 text-white text-sm py-3 rounded-lg hover:bg-indigo-700 transition font-medium">
                                    Enroll Sekarang
                                </button>
                            </form>
                        @endif

                        <p class="text-xs text-gray-500 mt-3 text-center">Akses seumur hidup termasuk semua update.</p>
                    </div>

                    {{-- DAFTAR SILABUS/MODUL (Daftar Isi) --}}
                    <div class="p-5 border border-gray-200 rounded-xl bg-gray-50 shadow-sm">
                        <h2 class="text-lg font-bold text-gray-800 mb-3 border-b pb-2">Daftar Isi Kursus</h2>
                        
                        <ul class="space-y-2 text-sm text-gray-700">
                            @forelse ($course->modules as $module)
                                <li>
                                    <span class="font-semibold">{{ $module->order }}. {{ $module->title }}</span>
                                    <ul class="ml-4 mt-1 space-y-1 text-xs text-gray-600 list-disc list-inside">
                                        @forelse ($module->materials as $material)
                                            <li class="{{ $isEnrolled ? 'text-green-700' : 'text-gray-400' }}">
                                                {{ $material->title }} 
                                                @if (!$isEnrolled) (Terkunci) @endif
                                            </li>
                                        @empty
                                            <li><span class="text-gray-500 italic">Belum ada materi.</span></li>
                                        @endforelse
                                    </ul>
                                </li>
                            @empty
                                <li class="text-gray-500 italic">Silabus belum tersedia.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection