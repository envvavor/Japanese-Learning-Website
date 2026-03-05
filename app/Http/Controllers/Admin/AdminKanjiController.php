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
        // PERBAIKAN: Menggunakan variabel $validated agar seragam
        $validated = $request->validate([
            'character' => 'required|string',
            'meaning' => 'required|string',
            'strokes' => 'required|json', // Jika dari form admin dikirim sebagai JSON string
            'category' => 'required|in:hiragana,katakana,kanji',
            'level' => 'nullable|integer',
            'stroke_order_image' => 'nullable|file|image',
            'kunyomi' => 'nullable|string',
            'onyomi' => 'nullable|string',
            
            // --- VALIDASI CONTOH KALIMAT ---
            'examples' => 'nullable|array',
            'examples.*.japanese_text' => 'required_with:examples|string',
            'examples.*.furigana_html' => 'nullable|string',
            'examples.*.meaning' => 'required_with:examples|string',
        ]);

        if ($request->hasFile('stroke_order_image')) {
            $imagePath = $request->file('stroke_order_image')->store('kanji_images', 'public');
            $validated['stroke_order_image'] = $imagePath;
        }

        $validated['strokes'] = json_decode($validated['strokes'], true);

        // 1. Simpan Data Kanji
        $kanji = Kanji::create($validated);

        // 2. Simpan Contoh Kalimat (Jika ada)
        if ($request->has('examples') && is_array($request->examples)) {
            foreach ($request->examples as $example) {
                if (!empty($example['japanese_text']) && !empty($example['meaning'])) {
                    $kanji->examples()->create([
                        'japanese_text' => $example['japanese_text'],
                        'furigana_html' => $example['furigana_html'] ?? null,
                        'meaning'       => $example['meaning'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.kanjis.index')->with('success', 'Kanji dan contoh kalimat berhasil ditambahkan.');
    }

    public function edit(Kanji $kanji)
    {
        // Load relasi examples agar bisa ditampilkan di form edit
        $kanji->load('examples');
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

            // --- VALIDASI CONTOH KALIMAT (Tambahkan juga di update) ---
            'examples' => 'nullable|array',
            'examples.*.japanese_text' => 'required_with:examples|string',
            'examples.*.furigana_html' => 'nullable|string',
            'examples.*.meaning' => 'required_with:examples|string',
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

        // 1. Update data Kanji utama
        $kanji->update($validated);

        // 2. Update Contoh Kalimat (Hapus yang lama, simpan yang baru)
        $kanji->examples()->delete();
        
        if ($request->has('examples') && is_array($request->examples)) {
            foreach ($request->examples as $example) {
                if (!empty($example['japanese_text']) && !empty($example['meaning'])) {
                    $kanji->examples()->create([
                        'japanese_text' => $example['japanese_text'],
                        'furigana_html' => $example['furigana_html'] ?? null,
                        'meaning'       => $example['meaning'],
                    ]);
                }
            }
        }

        return redirect()->route('admin.kanjis.index')->with('success', 'Kanji dan contoh kalimat berhasil diperbarui.');
    }

    public function destroy(Kanji $kanji)
    {
        $kanji->delete();
        return redirect()->route('admin.kanjis.index')->with('success', 'Kanji berhasil dihapus.');
    }
}