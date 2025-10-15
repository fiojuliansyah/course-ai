@extends('student.layouts.app')

@section('content')
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900">Kursus Saya</h1>
                <a href="{{ route('student.courses.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">&larr; Katalog Kursus</a>
            </div>
            
            <div class="mb-4">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Daftar Pendaftaran</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
                    @forelse ($enrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $status = $enrollment->status;
                            $isPaid = $status === 'paid' || $course->price == 0;
                            $isPending = $status === 'pending' && $course->price > 0;
                            
                            $progressValue = [0, 30, 80, 100][($loop->index) % 4]; 
                            $isCompleted = $progressValue == 100;
                            
                            $statusText = $isPaid ? ($isCompleted ? 'SELESAI' : 'AKTIF') : ($isPending ? 'PENDING' : 'BATAL');
                            $statusClass = $isPaid ? ($isCompleted ? 'bg-green-100 text-green-700' : 'bg-indigo-100 text-indigo-700') : 'bg-red-100 text-red-700';

                            if ($isPaid) {
                                $actionUrl = '#';
                                $actionText = 'Lanjutkan Belajar →';
                                $actionClass = 'bg-indigo-600 hover:bg-indigo-700';
                            } elseif ($isPending) {
                                $actionUrl = route('student.checkout', $enrollment);
                                $actionText = 'Bayar Sekarang';
                                $actionClass = 'bg-red-600 hover:bg-red-700';
                            } else {
                                $actionUrl = route('student.courses.enrolled.show', $course);
                                $actionText = 'Lihat Detail';
                                $actionClass = 'bg-gray-600 hover:bg-gray-700';
                            }
                        @endphp

                        {{-- Kartu Kursus Individual --}}
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition duration-300 hover:shadow-2xl">
                            
                            <img class="w-full h-32 object-cover" 
                                 src="{{ $course->thumbnail_path ? asset('storage/' . $course->thumbnail_path) : 'https://via.placeholder.com/400x150/0000ff/ffffff?text=My+Course' }}" 
                                 alt="Gambar Kursus {{ $course->title }}">

                            <div class="p-4">
                                <span class="px-2 py-1 text-xs font-medium {{ $statusClass }} rounded-full mb-2 block w-max">{{ $statusText }}</span>
                                
                                <p class="text-lg font-semibold text-gray-900 line-clamp-2 mt-1 mb-3">{{ $course->title }}</p>

                                @if ($isPaid)
                                    <div class="mb-3">
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="h-2 rounded-full {{ $isCompleted ? 'bg-green-600' : 'bg-indigo-600' }}" style="width: {{ $progressValue }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 mt-1 block">{{ $progressValue }}% Selesai</span>
                                    </div>
                                @else
                                    <div class="mb-3 h-6">
                                        <span class="text-sm font-semibold text-gray-500">Status pendaftaran: {{ ucfirst($status) }}</span>
                                    </div>
                                @endif
                                
                                <a href="{{ route('student.courses.enrolled.show', $course) }}" class="mt-2 block w-full text-center {{ $actionClass }} text-white text-sm py-2 rounded-lg font-medium transition">
                                    {{ $actionText }}
                                </a>
                                
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full p-4 text-center text-gray-500 bg-white rounded-xl shadow-lg border">
                            <p>Anda belum mendaftar di kursus manapun. Jelajahi <a href="{{ route('student.courses.index') }}" class="text-indigo-600 font-medium">Katalog Kursus</a>.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-8 flex justify-center">
                {{-- $enrollments->links('pagination::tailwind') --}}
            </div>

        </div>
    </main>
@endsection