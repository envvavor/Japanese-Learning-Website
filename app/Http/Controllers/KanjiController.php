<?php

namespace App\Http\Controllers;

use App\Models\Kanji;
use Illuminate\Http\Request;

class KanjiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kanji::select('id', 'character', 'meaning', 'category', 'level');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $kanjis = $query->orderBy('created_at', 'desc')->get();
        return response()->json($kanjis, 200);
    }

    public function show($character)
    {
        $kanji = Kanji::with('examples')->where('character', $character)->first();
        if (!$kanji) {
            return response()->json(['message' => 'Kanji tidak ditemukan'], 404);
        }
        return response()->json($kanji, 200);
    }

    public function store(Request $request)
    {
        // Laravel won't auto-convert a JSON string when sent via FormData,
        // so decode manually if necessary before validation.
        if ($request->has('strokes') && is_string($request->strokes)) {
            $request->merge(['strokes' => json_decode($request->strokes, true)]);
        }

        $validatedData = $request->validate([
            'character' => 'required|string',
            'meaning' => 'required|string',
            'strokes' => 'required|array',
            'category' => 'required|in:hiragana,katakana,kanji',
            'level' => 'nullable|integer',
            'stroke_order_image' => 'nullable|file|image',
            'kunyomi' => 'nullable|string',
            'onyomi' => 'nullable|string',
            
            // --- TAMBAHAN UNTUK CONTOH KALIMAT (ARRAY) ---
            'examples' => 'nullable|array',
            'examples.*.japanese_text' => 'required_with:examples|string',
            'examples.*.furigana_html' => 'nullable|string',
            'examples.*.meaning' => 'required_with:examples|string',
        ]);

        $data = [
            'meaning' => $request->meaning,
            'strokes' => $request->strokes,
            'category' => $request->category,
            'level' => $request->level,
            'kunyomi' => $request->kunyomi,
            'onyomi' => $request->onyomi,
        ];

        if ($request->hasFile('stroke_order_image')) {
            $path = $request->file('stroke_order_image')->store('stroke_images', 'public');
            $data['stroke_order_image'] = $path;
        }

        $kanji = Kanji::updateOrCreate(
            ['character' => $request->character],
            $data
        );

        // --- MULAI SIMPAN CONTOH KALIMAT ---
        
        // 1. Hapus contoh kalimat lama (Penting saat proses Update agar tidak duplikat)
        $kanji->examples()->delete();

        // 2. Simpan contoh kalimat baru dari request
        if ($request->has('examples') && is_array($request->examples)) {
            foreach ($request->examples as $example) {
                // Pastikan datanya tidak kosong
                if (!empty($example['japanese_text']) && !empty($example['meaning'])) {
                    $kanji->examples()->create([
                        'japanese_text' => $example['japanese_text'],
                        'furigana_html' => $example['furigana_html'] ?? null,
                        'meaning'       => $example['meaning'],
                    ]);
                }
            }
        }
        // --- SELESAI SIMPAN CONTOH KALIMAT ---

        return response()->json([
            'message' => 'Data Kanji Berhasil Disimpan!',
            'data' => $kanji
        ], 200);
    }
}