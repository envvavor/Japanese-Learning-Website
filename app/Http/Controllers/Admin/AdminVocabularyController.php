<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class AdminVocabularyController extends Controller
{
    public function index(Request $request)
    {
        $query = Vocabulary::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original', 'LIKE', "%{$search}%")
                  ->orWhere('furigana', 'LIKE', "%{$search}%")
                  ->orWhere('english', 'LIKE', "%{$search}%")
                  ->orWhere('indonesian', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('jlpt_level')) {
            if ($request->jlpt_level === 'none') {
                $query->whereNull('jlpt_level');
            } else {
                $query->where('jlpt_level', $request->jlpt_level);
            }
        }

        $vocabularies = $query->latest()->paginate(15)->withQueryString();

        return view('admin.vocabularies.index', compact('vocabularies'));
    }

    public function create()
    {
        return view('admin.vocabularies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'original'   => 'required|string|max:255',
            'furigana'   => 'nullable|string|max:255',
            'english'    => 'required|string',
            'indonesian' => 'nullable|string',
            'jlpt_level' => 'nullable|in:N1,N2,N3,N4,N5',
        ]);

        Vocabulary::create($validated);

        return redirect()->route('admin.vocabularies.index')->with('success', 'Kosakata berhasil ditambahkan.');
    }

    public function edit(Vocabulary $vocabulary)
    {
        return view('admin.vocabularies.edit', compact('vocabulary'));
    }

    public function update(Request $request, Vocabulary $vocabulary)
    {
        $validated = $request->validate([
            'original'   => 'required|string|max:255',
            'furigana'   => 'nullable|string|max:255',
            'english'    => 'required|string',
            'indonesian' => 'nullable|string',
            'jlpt_level' => 'nullable|in:N1,N2,N3,N4,N5',
        ]);

        $vocabulary->update($validated);

        return redirect()->route('admin.vocabularies.index')->with('success', 'Kosakata berhasil diperbarui.');
    }

    public function destroy(Vocabulary $vocabulary)
    {
        $vocabulary->delete();
        return redirect()->route('admin.vocabularies.index')->with('success', 'Kosakata berhasil dihapus.');
    }
}
