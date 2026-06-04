<?php

namespace App\Http\Controllers;

use App\Models\Vocabulary;
use App\Models\VocabularyFolder;
use App\Models\VocabularyFolderItem;
use App\Models\VocabularyFolderProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VocabularyFolderController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $adminFolders = VocabularyFolder::whereNull('user_id')
            ->where('is_public', true)
            ->withCount('items')
            ->get()
            ->map(fn($f) => $f->setAttribute('user_progress', $f->progressForUser($userId)));

        $myFolders = VocabularyFolder::where('user_id', $userId)
            ->withCount('items')
            ->latest()
            ->get()
            ->map(fn($f) => $f->setAttribute('user_progress', $f->progressForUser($userId)));

        return view('vocabulary-folders.index', compact('adminFolders', 'myFolders'));
    }

    public function show(VocabularyFolder $folder)
    {
        $userId = Auth::id();

        if ($folder->user_id !== null && $folder->user_id !== $userId) {
            abort(403);
        }

        $vocabularies = $folder->vocabularies()->paginate(40);

        $progressMap = VocabularyFolderProgress::where('user_id', $userId)
            ->where('folder_id', $folder->id)
            ->pluck('is_correct', 'vocabulary_id')
            ->toArray();

        $stats = $folder->progressForUser($userId);
        $canEdit = $folder->user_id === $userId;

        return view('vocabulary-folders.show', compact('folder', 'vocabularies', 'progressMap', 'stats', 'canEdit'));
    }

    public function create()
    {
        $colors = VocabularyFolder::$availableColors;
        return view('vocabulary-folders.create', compact('colors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:20',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_public'] = false;

        $folder = VocabularyFolder::create($validated);

        return redirect()->route('vocabulary-folders.show', $folder)
            ->with('success', 'Folder berhasil dibuat!');
    }

    public function edit(VocabularyFolder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $colors = VocabularyFolder::$availableColors;
        return view('vocabulary-folders.edit', compact('folder', 'colors'));
    }

    public function update(Request $request, VocabularyFolder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color'       => 'required|string|max:20',
        ]);

        $folder->update($validated);

        return redirect()->route('vocabulary-folders.show', $folder)
            ->with('success', 'Folder berhasil diperbarui!');
    }

    public function destroy(VocabularyFolder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $folder->delete();

        return redirect()->route('vocabulary-folders.index')
            ->with('success', 'Folder berhasil dihapus!');
    }

    public function addWord(Request $request, VocabularyFolder $folder)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
        ]);

        VocabularyFolderItem::firstOrCreate([
            'folder_id'     => $folder->id,
            'vocabulary_id' => $request->vocabulary_id,
        ]);

        return back()->with('success', 'Kata berhasil ditambahkan!');
    }

    public function addWordApi(Request $request, VocabularyFolder $folder)
    {
        $user = Auth::user();

        // Admin hanya ke folder admin, user biasa hanya ke folder miliknya
        if ($user->role === 'admin') {
            if ($folder->user_id !== null) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        } else {
            if ($folder->user_id !== $user->id) {
                return response()->json(['error' => 'Forbidden'], 403);
            }
        }

        $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
        ]);

        $created = VocabularyFolderItem::firstOrCreate([
            'folder_id'     => $folder->id,
            'vocabulary_id' => $request->vocabulary_id,
        ]);

        $isNew = $created->wasRecentlyCreated;

        return response()->json([
            'success' => true,
            'is_new'  => $isNew,
            'message' => $isNew ? 'Kata berhasil ditambahkan!' : 'Kata sudah ada di folder ini.',
        ]);
    }

    public function removeWord(VocabularyFolder $folder, Vocabulary $vocabulary)
    {
        if ($folder->user_id !== Auth::id()) {
            abort(403);
        }

        VocabularyFolderItem::where('folder_id', $folder->id)
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

    public function practice(VocabularyFolder $folder)
    {
        $userId = Auth::id();

        if ($folder->user_id !== null && $folder->user_id !== $userId) {
            abort(403);
        }

        $vocabularies = $folder->vocabularies()->get();

        if ($vocabularies->isEmpty()) {
            return redirect()->route('vocabulary-folders.show', $folder)
                ->with('error', 'Folder ini belum punya kosakata untuk dilatih!');
        }

        $progressMap = VocabularyFolderProgress::where('user_id', $userId)
            ->where('folder_id', $folder->id)
            ->pluck('is_correct', 'vocabulary_id')
            ->toArray();

        return view('vocabulary-folders.practice', compact('folder', 'vocabularies', 'progressMap'));
    }

    public function recordProgress(Request $request, VocabularyFolder $folder)
    {
        $userId = Auth::id();

        $request->validate([
            'vocabulary_id' => 'required|exists:vocabularies,id',
            'is_correct'    => 'required|boolean',
        ]);

        VocabularyFolderProgress::updateOrCreate(
            [
                'user_id'       => $userId,
                'folder_id'     => $folder->id,
                'vocabulary_id' => $request->vocabulary_id,
            ],
            [
                'is_correct'       => $request->is_correct,
                'attempts'         => \DB::raw('attempts + 1'),
                'last_practiced_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function finishPractice(Request $request, VocabularyFolder $folder)
    {
        $request->validate([
            'correct'   => 'required|integer|min:0',
            'total'     => 'required|integer|min:1',
        ]);

        $score = ($request->correct / $request->total) * 100;
        $xp = (int) ($request->correct * 2);

        if ($xp > 0) {
            Auth::user()->addXp($xp);
        }

        return response()->json([
            'success' => true,
            'xp_earned' => $xp,
            'score' => round($score),
        ]);
    }
}
