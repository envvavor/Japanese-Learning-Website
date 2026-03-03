<?php

namespace App\Http\Controllers;

use App\Models\Kanji;
use Illuminate\Http\Request;

class KanjiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kanji::select('id', 'character', 'meaning', 'category');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $kanjis = $query->orderBy('created_at', 'desc')->get();
        return response()->json($kanjis, 200);
    }

    public function show($character)
    {
        $kanji = Kanji::where('character', $character)->first();
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

        $request->validate([
            'character' => 'required|string',
            'meaning' => 'required|string',
            'strokes' => 'required|array',
            'category' => 'required|in:hiragana,katakana,kanji',
            'level' => 'nullable|integer',
            'stroke_order_image' => 'nullable|file|image',
            'kunyomi' => 'nullable|string',
            'onyomi' => 'nullable|string',
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

        return response()->json([
            'message' => 'Data Kanji Berhasil Disimpan!',
            'data' => $kanji
        ], 200);
    }
}