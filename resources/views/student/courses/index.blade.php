@extends('student.layouts.app')

@section('content')
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900">Katalog Semua Kursus</h1>
                <a href="{{ route('student.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Kembali ke Dashboard</a>
            </div>
            
            {{-- Filter dan Search Bar --}}
            <form method="GET" action="{{ route('student.courses.index') }}" class="flex flex-col sm:flex-row justify-between items-center mb-8 space-y-4 sm:space-y-0 sm:space-x-4">
                
                {{-- Input Pencarian --}}
                <input type="text" name="q" placeholder="Cari judul kursus..." 
                       class="w-full sm:w-1/3 p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm"
                       value="{{ $searchTerm ?? '' }}">
                
                <div class="flex w-full sm:w-auto space-x-4">
                    {{-- Filter Kategori --}}
                    <select name="category_id" class="p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ ($categoryId ?? null) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Tombol Submit --}}
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                        Filter
                    </button>
                </div>
            </form>

            {{-- GRID KURSUS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @forelse ($courses as $course)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition duration-300 hover:shadow-2xl hover:translate-y-[-2px]">
                        
                        <img class="w-full h-40 object-cover" 
                             src="{{ $course->thumbnail_path ? asset('storage/' . $course->thumbnail_path) : 'https://via.placeholder.com/600x400/9333ea/ffffff?text=E-Course' }}" 
                             alt="Gambar Kursus {{ $course->title }}">

                        <div class="p-4">
                            
                            <h3 class="text-lg font-bold text-gray-800 line-clamp-2 mb-2">{{ $course->title }}</h3>
                            
                            <div class="flex justify-between items-end border-t pt-2">
                                
                                @php
                                    $price = $course->price ?? 0;
                                @endphp
                                @if ($price > 0)
                                    <p class="text-xl font-extrabold text-indigo-600">
                                        Rp{{ number_format($price, 0, ',', '.') }}
                                    </p>
                                @else
                                    <p class="text-xl font-extrabold text-green-600">
                                        Gratis
                                    </p>
                                @endif
                                
                                <p class="text-xs text-gray-500">
                                    {{ $course->category->name ?? 'Umum' }}
                                </p>
                            </div>

                            <a href="{{ route('student.courses.show', $course) }}"
                               class="mt-4 block w-full text-center bg-indigo-600 text-white text-sm py-2 rounded-lg hover:bg-indigo-700 transition font-medium">
                                Lihat & Enroll
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full p-10 text-center text-gray-500 bg-white rounded-xl shadow-lg border">
                        <p>Maaf, belum ada kursus yang tersedia saat ini.</p>
                    </div>
                @endforelse
            </div>
            
            {{-- Paginasi Dinamis --}}
            <div class="mt-8 flex justify-center">
                {{ $courses->links('pagination::tailwind') }}
            </div>
        </div>
    </main>
@endsection