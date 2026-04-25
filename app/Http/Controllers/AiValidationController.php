<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiValidationController extends Controller
{
    public function check(Request $request)
    {
        // Pastikan data dari Javascript masuk dengan benar
        $request->validate([
            'character' => 'required|string',
            'image_base64' => 'required|string',
        ]);

        try {
            $response = Http::post('http://127.0.0.1:5000/predict', [
                'target_character' => $request->character,
                'image_base64' => $request->image_base64
            ]);

            return response()->json($response->json());
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Gagal terhubung ke server AI Python.',
                'error' => $e->getMessage()
            ]);
        }
    }
}