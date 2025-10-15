<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->query('q');
        $categoryId = $request->query('category_id');

        $query = Course::with('category')
                         ->latest();

        if ($searchTerm) {
            $query->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('short_description', 'like', '%' . $searchTerm . '%');
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $courses = $query->paginate(12)->appends($request->query());
        $categories = Category::all();

        return view('student.courses.index', compact('courses', 'categories', 'searchTerm', 'categoryId'));
    }

    public function show(Course $course)
    {
        return view('student.courses.show', compact('course'));
    }
}
