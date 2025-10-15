<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage; // Tambahkan ini untuk Storage

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category')->orderBy('created_at', 'desc')->paginate(9);
        
        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'short_description' => 'nullable|string|max:200',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi Thumbnail
        ]);

        $slug = Str::slug($request->title) . '-' . time();
        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            // Simpan gambar ke storage/app/public/thumbnails
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Course::create([
            'title' => $request->title,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'short_description' => $request->short_description,
            'content' => $request->content,
            'status' => 'draft',
            'thumbnail_path' => $thumbnailPath, // Simpan path yang dikembalikan oleh Storage
        ]);

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function edit(Course $course)
    {
        $categories = Category::orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer|min:0',
            'short_description' => 'nullable|string|max:200',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi Thumbnail
        ]);
        
        $slug = ($request->title !== $course->title) 
                ? Str::slug($request->title) . '-' . time() 
                : $course->slug;

        $thumbnailPath = $course->thumbnail_path;

        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama jika ada
            if ($course->thumbnail_path) {
                Storage::disk('public')->delete($course->thumbnail_path);
            }
            // Simpan gambar baru
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update([
            'title' => $request->title,
            'slug' => $slug,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'short_description' => $request->short_description,
            'content' => $request->content,
            'status' => $request->status ?? $course->status,
            'thumbnail_path' => $thumbnailPath,
        ]);

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Kursus berhasil diperbarui!');
    }

    public function destroy(Course $course)
    {
        if ($course->thumbnail_path) {
            Storage::disk('public')->delete($course->thumbnail_path);
        }
        
        $course->delete();
        
        return redirect()->route('admin.courses.index')
                         ->with('success', 'Kursus berhasil dihapus!');
    }
}