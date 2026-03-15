<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatasetController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $request->validate([
            'character' => 'required|string',
            'image_base64' => 'required|string',
        ]);

        $char = $request->character;
        $imageBase64 = $request->image_base64;

        try {
            // 2. Bersihkan string Base64 dari embel-embel "data:image/png;base64,"
            $imageParts = explode(";base64,", $imageBase64);
            
            if (count($imageParts) == 2) {
                $imageBase64Decoded = base64_decode($imageParts[1]);
            } else {
                return response()->json(['success' => false, 'message' => 'Format gambar tidak valid.']);
            }

            // 3. Buat nama file yang unik (Contoh: 日_1709456_abc123.png)
            $fileName = $char . '_' . time() . '_' . uniqid() . '.png';
            
            // 4. Tentukan folder penyimpanan (Otomatis dibuatkan folder per huruf)
            // Path: storage/app/public/dataset/日/日_1709456_abc123.png
            $folderPath = 'dataset/' . $char;
            $filePath = $folderPath . '/' . $fileName;

            // 5. Simpan gambar ke dalam storage
            Storage::disk('public')->put($filePath, $imageBase64Decoded);

            return response()->json([
                'success' => true, 
                'message' => 'Berhasil disimpan ke dataset!',
                'path' => $filePath
            ]);

        } catch (\Exception $e) {
            // Jika error, kembalikan pesan error agar tidak membuat web crash
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}