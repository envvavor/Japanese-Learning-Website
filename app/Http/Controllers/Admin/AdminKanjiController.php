<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kanji;
use Illuminate\Http\Request;

class AdminKanjiController extends Controller
{
    public function index()
    {
        $kanjis = Kanji::latest()->paginate(10);
        return view('admin.kanjis.index', compact('kanjis'));
    }

    public function create()
    {
        return view('admin.kanjis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'character' => 'required|string|unique:kanjis',
            'meaning' => 'required|string',
            'category' => 'required|in:hiragana,katakana,kanji',
            'level' => 'nullable|integer',
            'kunyomi' => 'nullable|string',
            'onyomi' => 'nullable|string',
            'stroke_order_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
            'strokes' => 'required|json',
        ]);

        if ($request->hasFile('stroke_order_image')) {
            $imagePath = $request->file('stroke_order_image')->store('kanji_images', 'public');
            $validated['stroke_order_image'] = $imagePath;
        }

        $validated['strokes'] = json_decode($validated['strokes'], true);

        Kanji::create($validated);

        return redirect()->route('admin.kanjis.index')->with('success', 'Kanji berhasil ditambahkan.');
    }

    public function edit(Kanji $kanji)
    {
        return view('admin.kanjis.edit', compact('kanji'));
    }

    public function update(Request $request, Kanji $kanji)
    {
        $validated = $request->validate([
            'character' => 'required|string|unique:kanjis,character,' . $kanji->id,
            'meaning' => 'required|string',
            'category' => 'required|in:hiragana,katakana,kanji',
            'level' => 'nullable|integer',
            'kunyomi' => 'nullable|string',
            'onyomi' => 'nullable|string',
            'stroke_order_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'strokes' => 'required|json',
        ]);

        if ($request->hasFile('stroke_order_image')) {
            if ($kanji->stroke_order_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kanji->stroke_order_image);
            }
            $imagePath = $request->file('stroke_order_image')->store('kanji_images', 'public');
            $validated['stroke_order_image'] = $imagePath;
        } else {
            unset($validated['stroke_order_image']);
        }

        $validated['strokes'] = json_decode($validated['strokes'], true);

        $kanji->update($validated);

        return redirect()->route('admin.kanjis.index')->with('success', 'Kanji berhasil diperbarui.');
    }

    public function destroy(Kanji $kanji)
    {
        $kanji->delete();
        return redirect()->route('admin.kanjis.index')->with('success', 'Kanji berhasil dihapus.');
    }
}
