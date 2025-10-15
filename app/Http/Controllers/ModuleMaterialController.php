<?php

namespace App\Http\Controllers;

use App\Models\CourseModule;
use Illuminate\Http\Request;
use App\Models\ModuleMaterial;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ModuleMaterialController extends Controller
{
    public function edit(ModuleMaterial $material)
    {
        return response()->json($material);
    }

    public function update(Request $request, ModuleMaterial $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'order' => 'nullable|integer',
        ]);

        $material->update($request->only('title', 'content', 'order'));

        $courseId = CourseModule::find($material->module_id)->course_id;

        return redirect()->route('admin.courses.syllabus.index', $courseId)
                         ->with('success', 'Materi berhasil diperbarui.');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:course_modules,id',
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
        ]);

        $order = ModuleMaterial::where('module_id', $request->module_id)->max('order') + 1;

        ModuleMaterial::create([
            'module_id' => $request->module_id,
            'title' => $request->title,
            'content' => $request->content,
            'order' => $order,
        ]);

        $courseId = CourseModule::find($request->module_id)->course_id;

        return redirect()->route('admin.courses.syllabus.index', $courseId)
                         ->with('success', 'Materi baru berhasil ditambahkan.');
    }

    public function destroy(ModuleMaterial $material)
    {
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $courseId = $material->module->course_id;
        $material->delete();

        return redirect()->route('admin.courses.syllabus.index', $courseId)
                         ->with('success', 'Materi berhasil dihapus.');
    }
}