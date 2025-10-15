<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use DB;

class StudentCheckoutController extends Controller
{
    /**
     * Memulai proses enrollment/checkout.
     */
    public function enroll(Course $course)
    {
        $user = Auth::user();

        $existingEnrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if ($existingEnrollment && $existingEnrollment->status !== 'pending') {
            return redirect()->route('student.dashboard')->with('error', 'Anda sudah terdaftar di kursus ini!');
        }

        $enrollment = Enrollment::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['status' => 'pending']
        );

        $amount = $course->price ?? 0;

        if ($amount > 0) {
            $transaction = Transaction::create([
                'enrollment_id' => $enrollment->id,
                'invoice_id' => 'INV-' . strtoupper(Str::random(8)) . $enrollment->id,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            return redirect()->route('student.checkout', $enrollment);
        }

        $enrollment->update(['status' => 'paid']);
        return redirect()->route('student.dashboard')->with('success', 'Anda berhasil terdaftar di kursus GRATIS!');
    }


    public function checkout(Enrollment $enrollment)
    {
        if (Auth::id() !== $enrollment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $transaction = $enrollment->transaction;

        if (!$transaction) {
            return redirect()->route('student.dashboard')->with('error', 'Transaksi tidak ditemukan.');
        }
        
        if ($enrollment->status === 'paid') {
             return redirect()->route('student.dashboard')->with('success', 'Pembayaran sudah selesai!');
        }

        return view('student.checkout.index', compact('enrollment', 'transaction'));
    }

    public function confirmPayment(Request $request, Enrollment $enrollment)
    {
        if (Auth::id() !== $enrollment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return DB::transaction(function () use ($enrollment) {
            $transaction = $enrollment->transaction;
            
            $transaction->update(['status' => 'paid', 'payment_method' => 'bank_transfer']);
            
            $enrollment->update(['status' => 'paid']);

            return redirect()->route('student.dashboard')->with('success', 'Pembayaran berhasil dikonfirmasi! Selamat belajar!');
        });
    }
}