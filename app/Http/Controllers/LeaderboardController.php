<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function index()
    {
        // Ambil top 50 user berdasarkan level (desc), lalu XP (desc)
        $leaderboard = User::select('id', 'name', 'google_avatar', 'level', 'xp', 'next_level_xp')
            ->orderByDesc('level')
            ->orderByDesc('xp')
            ->take(50)
            ->get();

        // Cari rank user yang sedang login
        $currentUser = Auth::user();
        $currentRank = User::where(function ($q) use ($currentUser) {
                $q->where('level', '>', $currentUser->level)
                  ->orWhere(function ($q2) use ($currentUser) {
                      $q2->where('level', $currentUser->level)
                         ->where('xp', '>', $currentUser->xp);
                  });
            })->count() + 1;

        return view('leaderboard', compact('leaderboard', 'currentUser', 'currentRank'));
    }
}
