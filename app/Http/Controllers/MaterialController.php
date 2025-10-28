<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\ModuleMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Arr;
use Exception;

class MaterialController extends Controller
{
    public function index(Course $course)
    {
        $modules = CourseModule::with('materials')->where('course_id', $course->id)->orderBy('order')->get();
        return view('admin.materials.index', compact('course', 'modules'));
    }

    public function generateModules(Request $request, Course $course)
    {
        set_time_limit(300);

        $request->validate([
            'material_file' => 'required|file|mimes:pdf|max:10240',
        ]);

        if (!$request->file('material_file')->isValid()) {
            return response()->json(['error' => 'File PDF tidak valid.'], 400);
        }

        try {
            $file = $request->file('material_file');
            $fullPdfPath = $file->getRealPath();

            $parser = new Parser();
            $pdf = $parser->parseFile($fullPdfPath);
            $pdfText = $pdf->getText();
            
            $limitedPdfText = substr($pdfText, 0, 80000); 

            $courseTitle = $course->title;
            
            $prompt = "Kamu adalah spesialis ekstraksi data dan editor konten. Tugasmu adalah: 1) Ekstrak **SEMUA** detail penting dari teks mentah di bawah ini, DILARANG KERAS MELAKUKAN PEMERINGKASAN. 2) Strukturkan teks hasil ekstraksi menjadi Modul dan Materi kursus. 3) Untuk konten setiap materi, format teks yang sudah diekstrak ke dalam **MARKDOWN TERSTRUKTUR** (Gunakan Tabel Markdown, ###, dan List). \n\n--- TEKS MENTAH DARI PDF ---\n{$limitedPdfText}\n\n--- INSTRUKSI OUTPUT ---\n1. Hasilkan output dalam format JSON VALID dengan field 'modules' (array of objects) dan 'summary'.\n2. Setiap Modul harus memiliki 'title', 'description', dan 'materials' (array of objects).\n3. Setiap Materi harus memiliki 'title' dan 'content_detail'.\n4. Field 'content_detail' WAJIB diisi dengan hasil EKSTRAKSI LENGKAP dan diformat MARKDOWN.\n\nHasilkan JSON output sekarang.";

            $apiKey = env('OPENAI_API_KEY');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(300) 
            ->post('https://api.openai.com/v1/chat/completions', [
                'model'     => 'gpt-4-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 4000,
                'temperature' => 0.1,
                'response_format' => ['type' => 'json_object'],
            ]);

            if (!$response->successful()) {
                 return response()->json(['error' => 'OpenAI API Error: ' . $response->status()], 502);
            }

            $content = Arr::get($response->json(), 'choices.0.message.content', '');
            $payload = json_decode(trim($content, " \n\r\t\v\0"), true);

            // --- DEBUGGING & VALIDASI KONTEN ---
            if (json_last_error() !== JSON_ERROR_NONE || !isset($payload['modules'])) {
                 // LOG: Simpan respons mentah ke log jika parsing gagal
                 \Log::error('OpenAI JSON Parsing Failed. Raw Response:', ['content' => $content, 'error' => json_last_error_msg()]);
                 throw new Exception("Gagal memproses JSON output AI. Periksa storage/logs.");
            }

            // Simpan Modul dan Materi ke Database
            $moduleOrder = CourseModule::where('course_id', $course->id)->max('order') + 1;
            
            foreach ($payload['modules'] as $moduleData) { // Loop menggunakan 'modules'
                $module = CourseModule::create([
                    'course_id'   => $course->id,
                    'title'       => $moduleData['title'] ?? "Modul Baru",
                    'description' => $moduleData['description'] ?? null,
                    'order'       => $moduleOrder++,
                ]);

                $materialOrder = 1;
                foreach ($moduleData['materials'] ?? [] as $materialData) {
                     // FINAL CHECK: Jika content_detail kosong, set fallback string agar tidak null/kosong
                     $generatedContent = $materialData['content_detail'] ?? 'Konten materi tidak dapat diekstrak secara memadai. Perlu review manual.';
                     
                     ModuleMaterial::create([
                        'module_id' => $module->id,
                        'title'     => $materialData['title'] ?? "Materi {$materialOrder}",
                        'file_path' => null, 
                        'content'   => $generatedContent, 
                        'order'     => $materialOrder++,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Modul dan konten berhasil diekstrak dan distrukturkan!',
                'summary' => $payload['summary'] ?? 'Ringkasan tidak tersedia.'
            ], 201);

        } catch (Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
}