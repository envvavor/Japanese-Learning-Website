<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use App\Models\VocabularyFolder;
use App\Models\VocabularyFolderItem;
use Illuminate\Http\Request;

class AdminVocabularyFolderController extends Controller
{
    public function index()
    {
        $folders = VocabularyFolder::whereNull('user_id')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('admin.vocabulary-folders.index', compact('folders'));
    }

    public function create()
    {
        $colors = VocabularyFolder::$availableColors;
        return view('admin.vocabulary-folders.create', compact('colors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:20',
        ]);

        $validated['user_id'] = null;
        $validated['is_public'] = true;

        VocabularyFolder::create($validated);

        return redirect()->route('admin.vocabulary-folders.index')
            ->with('success', 'Folder kosakata berhasil dibuat!');
    }

    public function show(VocabularyFolder $vocabulary_folder)
    {
        $folder = $vocabulary_folder;
        $vocabularies = $folder->vocabularies()->paginate(30);

        return view('admin.vocabulary-folders.show', compact('folder', 'vocabularies'));
    }

    public function edit(VocabularyFolder $vocabulary_folder)
    {
        $folder = $vocabulary_folder;
        $colors = VocabularyFolder::$availableColors;
        return view('admin.vocabulary-folders.edit', compact('folder', 'colors'));
    }

    public function update(Request $request, VocabularyFolder $vocabulary_folder)
    {
        $folder = $vocabulary_folder;

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:20',
        ]);

        $folder->update($validated);

        return redirect()->route('admin.vocabulary-folders.index')
            ->with('success', 'Folder kosakata berhasil diperbarui!');
    }

    public function destroy(VocabularyFolder $vocabulary_folder)
    {
        $vocabulary_folder->delete();

        return redirect()->route('admin.vocabulary-folders.index')
            ->with('success', 'Folder kosakata berhasil dihapus!');
    }

    public function addWord(Request $request, VocabularyFolder $vocabulary_folder)
    {
        $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
        ]);

        VocabularyFolderItem::firstOrCreate([
            'folder_id'     => $vocabulary_folder->id,
            'vocabulary_id' => $request->vocabulary_id,
        ]);

        return back()->with('success', 'Kata berhasil ditambahkan ke folder!');
    }

    public function removeWord(VocabularyFolder $vocabulary_folder, Vocabulary $vocabulary)
    {
        VocabularyFolderItem::where('folder_id', $vocabulary_folder->id)
            ->where('vocabulary_id', $vocabulary->id)
            ->delete();

        return back()->with('success', 'Kata berhasil dihapus dari folder!');
    }

    public function searchVocabulary(Request $request)
    {
        $q = $request->input('q', '');

        $results = Vocabulary::query()
            ->when($q, fn($query) => $query->search($q))
            ->orderBy('jlpt_level')
            ->limit(20)
            ->get(['id', 'original', 'furigana', 'english', 'indonesian', 'jlpt_level']);

        return response()->json($results);
    }
}
