<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialCompletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningProgressController extends Controller
{

    public function completeMaterial(Request $request, Material $material)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $completion = MaterialCompletion::firstOrCreate([
            'user_id' => Auth::id(),
            'material_id' => $material->id,
        ]);


        return response()->json([
            'status' => 'success', 
            'message' => 'Materi berhasil ditandai selesai.',
            'completed_at' => $completion->completed_at->format('d M Y H:i'),
        ]);
    }
}