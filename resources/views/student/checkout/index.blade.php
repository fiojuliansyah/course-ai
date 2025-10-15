@extends('student.layouts.app')

@section('content')
    <main class="max-w-xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Selesaikan Pembayaran</h1>
        
        <div class="bg-white shadow-xl rounded-xl p-8 border border-indigo-100 space-y-6">
            
            <p class="text-indigo-600 font-semibold text-lg">Invoice ID: {{ $transaction->invoice_id }}</p>
            
            {{-- Detail Kursus --}}
            <div class="border-b pb-4">
                <p class="text-sm text-gray-500">Kursus:</p>
                <p class="text-2xl font-bold text-gray-800">{{ $enrollment->course->title }}</p>
            </div>

            {{-- Total Pembayaran --}}
            <div class="flex justify-between items-center pt-4 border-t">
                <p class="text-xl font-semibold">Total Tagihan:</p>
                <p class="text-3xl font-extrabold text-red-600">
                    Rp{{ number_format($transaction->amount, 0, ',', '.') }}
                </p>
            </div>
            
            {{-- Instruksi Pembayaran (Simulasi) --}}
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <p class="font-semibold text-yellow-800 mb-2">Instruksi Pembayaran:</p>
                <p class="text-sm text-gray-700">Silakan transfer jumlah tagihan ke rekening berikut dalam waktu 24 jam:</p>
                <ul class="mt-3 text-sm font-mono space-y-1">
                    <li>**BANK:** BNI (Bank Nasional Indonesia)</li>
                    <li>**No. Rek:** 1234 5678 90</li>
                    <li>**Atas Nama:** PT. Ecourse Digital</li>
                </ul>
            </div>

            {{-- Tombol Konfirmasi (Simulasi Action) --}}
            <form action="{{ route('student.checkout.confirm', $enrollment) }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-green-600 text-white text-lg py-3 rounded-lg hover:bg-green-700 transition font-medium">
                    Simulasi: Konfirmasi Pembayaran Berhasil
                </button>
            </form>
            
            <a href="{{ route('student.dashboard') }}" class="block text-center text-sm text-gray-500 hover:text-indigo-600">Kembali ke Dashboard</a>
            
        </div>
        
    </main>
@endsection