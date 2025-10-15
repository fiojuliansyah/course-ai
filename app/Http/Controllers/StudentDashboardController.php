<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'active_courses_count' => 3,
            'total_progress_percent' => 72,
            'certificates_count' => 1,
            'last_quiz_score' => 95,
        ];
        
        $enrollments = [

        ];
        
        $latestCourses = Course::latest()->take(3)->get();
        
        return view('student.dashboard', compact('user', 'stats', 'enrollments', 'latestCourses'));
    }

    public function myCourse()
    {
        $userId = Auth::id();
        $enrollments = Enrollment::where('user_id', $userId)
                                 ->with(['course', 'transaction'])
                                 ->latest()
                                 ->paginate(10);
        
        return view('student.courses.enrolled.index', compact('enrollments'));
    }

    public function myCourseShow(Course $course)
    {
        $user = Auth::user();

        $course->load('modules.materials');
        $enrollment = Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();

        $isEnrolled = $enrollment && $enrollment->status === 'paid';
        $isPending = $enrollment && $enrollment->status === 'pending';
        
        if (!$isEnrolled) {
            if ($isPending) {
                return redirect()->route('student.checkout', $enrollment)->with('error', 'Pembayaran kursus ini masih tertunda.');
            }
            return redirect()->route('student.courses.show', $course)->with('error', 'Anda harus mendaftar dan membayar kursus ini terlebih dahulu.');
        }

        return view('student.courses.enrolled.show', compact('course', 'enrollment', 'isEnrolled'));
    }
}
