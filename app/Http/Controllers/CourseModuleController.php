<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CourseModule;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    public function edit(CourseModule $module)
    {
        return response()->json($module);
    }

    public function update(Request $request, CourseModule $module)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $module->update($request->only('title', 'description', 'order'));

        return redirect()->route('admin.courses.material.index', $module->course_id)
                         ->with('success', 'Modul berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $order = CourseModule::where('course_id', $request->course_id)->max('order') + 1;

        CourseModule::create([
            'course_id' => $request->course_id,
            'title' => $request->title,
            'description' => $request->description,
            'order' => $order,
        ]);

        return redirect()->route('admin.courses.material.index', $request->course_id)
                         ->with('success', 'Modul baru berhasil ditambahkan secara manual.');
    }

    public function destroy(CourseModule $module)
    {
        $courseId = $module->course_id;
        $module->delete();

        return redirect()->route('admin.courses.material.index', $courseId)
                         ->with('success', 'Modul berhasil dihapus.');
    }
}