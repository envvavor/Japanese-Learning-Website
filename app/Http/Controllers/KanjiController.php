<?php

namespace App\Http\Controllers;

use App\Models\Kanji;
use Illuminate\Http\Request;

class KanjiController extends Controller
{
    public function index()
    {
        $kanjis = Kanji::select('id', 'character', 'meaning')->orderBy('created_at', 'desc')->get();
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
        $request->validate([
            'character' => 'required|string',
            'meaning' => 'required|string',
            'strokes' => 'required|array'
        ]);

        $kanji = Kanji::updateOrCreate(
            ['character' => $request->character],
            [
                'meaning' => $request->meaning,
                'strokes' => $request->strokes
            ]
        );

        return response()->json([
            'message' => 'Data Kanji Berhasil Disimpan!',
            'data' => $kanji
        ], 200);
    }
}