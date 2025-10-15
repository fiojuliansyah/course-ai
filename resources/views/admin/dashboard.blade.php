@extends('admin.layouts.app')

@section('content')
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">

            <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Dashboard E-Course Admin</h1>
            
            {{-- Grid Statistik (Dinamis) --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                
                {{-- Card 1: Total Kursus --}}
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-5 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Kursus</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_courses']) }}</p>
                        <p class="ml-2 text-sm font-medium text-green-600">+{{ $stats['new_courses_this_month'] }} bulan ini</p>
                    </div>
                </div>
                
                {{-- Card 2: Total Pengguna --}}
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-5 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Pengguna</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_users']) }}</p>
                        <p class="ml-2 text-sm font-medium text-green-600">+{{ $stats['new_users_this_month'] }} baru</p>
                    </div>
                </div>

                {{-- Card 3: Pendapatan Bulan Ini (Simulasi) --}}
                @php
                    $isPositive = $stats['revenue_change'] >= 0;
                    $colorClass = $isPositive ? 'text-green-600' : 'text-red-600';
                    $sign = $isPositive ? '+' : '';
                @endphp
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-5 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
                    <p class="text-sm font-medium text-gray-500 truncate">Pendapatan Bulan Ini</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-xl font-semibold text-gray-900">Rp{{ number_format($stats['revenue_this_month'], 0, ',', '.') }}</p>
                        <p class="ml-2 text-sm font-medium {{ $colorClass }}">{{ $sign }}{{ $stats['revenue_change'] }}%</p>
                    </div>
                </div>

                {{-- Card 4: Total Pertanyaan/Kuis --}}
                <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 p-5 transition duration-300 hover:shadow-2xl hover:border-indigo-200">
                    <p class="text-sm font-medium text-gray-500 truncate">Total Pertanyaan Kuis</p>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">{{ number_format($stats['total_questions']) }}</p>
                        <p class="ml-2 text-sm font-medium text-gray-500">tersedia</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Kolom Kiri (Grafik Tren) --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Tren Pendaftaran (6 Bulan Terakhir)</h2>
                        <div class="h-64 bg-gray-50 border border-dashed border-gray-300 flex items-center justify-center text-gray-500 rounded-lg">
                            [ Area Placeholder untuk Grafik Tren Menggunakan Library JS]
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan (Kursus Populer - Dinamis) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white shadow-xl rounded-xl p-6 border border-gray-100">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Kursus Paling Populer</h2>
                        <ul class="space-y-4">
                            @forelse ($popularCourses as $index => $course)
                                <li class="flex justify-between items-center text-gray-700 @if($index < count($popularCourses) - 1) border-b pb-2 @endif">
                                    <span class="font-medium">{{ $course->title }}</span>
                                    <span class="text-sm text-indigo-600">{{ number_format($course->students_count) }} Siswa</span>
                                </li>
                            @empty
                                <li class="text-gray-500 text-sm">Tidak ada kursus yang terdaftar.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </main>
@endsection