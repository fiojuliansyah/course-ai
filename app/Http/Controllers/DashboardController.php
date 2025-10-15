<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Question;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCourses = Course::count();
        $totalUsers = User::count();
        $totalQuestions = Question::count();
        
        $startOfMonth = Carbon::now()->startOfMonth();
        $newCoursesThisMonth = Course::where('created_at', '>=', $startOfMonth)->count();
        $newUsersThisMonth = User::where('created_at', '>=', $startOfMonth)->count();
        
        $revenueThisMonth = 4500000;
        $previousRevenue = 5000000;
        
        $popularCourses = Course::orderByDesc('students_count')
                                ->limit(3)
                                ->get(['title', 'students_count']);
        
        $revenueChange = $previousRevenue > 0 
                         ? round((($revenueThisMonth - $previousRevenue) / $previousRevenue) * 100, 1) 
                         : 0;

        $stats = [
            'total_courses' => $totalCourses,
            'new_courses_this_month' => $newCoursesThisMonth,
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsersThisMonth,
            'total_questions' => $totalQuestions, 
            
            'revenue_this_month' => $revenueThisMonth,
            'revenue_change' => $revenueChange,
        ];

        return view('admin.dashboard', compact('stats', 'popularCourses'));
    }
}