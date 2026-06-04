<?php

namespace App\Http\Controllers;

use App\Models\Vocabulary;
use App\Models\VocabularyFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VocabularyController extends Controller
{
    /**
     * Halaman daftar kosakata dengan filter & search.
     */
    public function index(Request $request)
    {
        $level = $request->input('level', '');
        $search = $request->input('q', '');

        $query = Vocabulary::query()
            ->orderByRaw("CASE WHEN jlpt_level IS NULL THEN 1 ELSE 0 END")
            ->orderBy('jlpt_level')
            ->orderBy('original');

        if ($level === 'none') {
            $query->whereNull('jlpt_level');
        } elseif ($level && in_array(strtoupper($level), ['N1','N2','N3','N4','N5'])) {
            $query->byLevel($level);
        }

        if ($search) {
            $query->search($search);
        }

        $vocabularies = $query->paginate(40)->withQueryString();

        // Hitung total per level untuk tampilan badge
        $counts = Vocabulary::query()
            ->selectRaw('jlpt_level, count(*) as total')
            ->groupBy('jlpt_level')
            ->pluck('total', 'jlpt_level');

        // Ambil folder milik user + folder admin (public) untuk tombol simpan
        $folders = collect();
        if (Auth::check()) {
            $user = Auth::user();
            $query = VocabularyFolder::withCount('items')->orderBy('name');
            
            if ($user->role === 'admin') {
                $folders = $query->whereNull('user_id')->get();
            } else {
                $folders = $query->where('user_id', $user->id)->get();
            }
        }

        return view('vocabularies.index', compact('vocabularies', 'level', 'search', 'counts', 'folders'));
    }

    /**
     * API: Search kosakata (untuk fitur autocomplete/search live).
     */
    public function search(Request $request)
    {
        $q = $request->input('q', '');
        $level = $request->input('level', '');

        $query = Vocabulary::query();

        if ($q) {
            $query->search($q);
        }

        if ($level) {
            $query->byLevel($level);
        }

        return response()->json(
            $query->orderBy('jlpt_level')->limit(30)->get()
        );
    }
}