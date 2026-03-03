<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes - User dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard')->middleware('auth');

// Protected routes - Admin panel
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', function () {
            $totalKanji = \App\Models\Kanji::where('category', 'kanji')->count();
            $totalHiragana = \App\Models\Kanji::where('category', 'hiragana')->count();
            $totalKatakana = \App\Models\Kanji::where('category', 'katakana')->count();
            return view('admin.dashboard', compact('totalKanji', 'totalHiragana', 'totalKatakana'));
        })->name('dashboard');
        
        Route::resource('kanjis', \App\Http\Controllers\Admin\AdminKanjiController::class);
    });

Route::get('/list/{category?}', function ($category = null) {
    return view('welcome', ['category' => $category]);
})->name('list-kanji')->middleware('auth');

// kategori terpisah dengan halaman sendiri
Route::get('/hiragana', function () {
    return view('welcome', ['category' => 'hiragana']);
})->middleware('auth');
Route::get('/katakana', function () {
    return view('welcome', ['category' => 'katakana']);
})->middleware('auth');
Route::get('/kanji', function () {
    return view('welcome', ['category' => 'kanji']);
})->middleware('auth');

// detail kanji untuk melihat informasi
Route::get('/kanji/{character}', function ($character) {
    return view('kanji-detail', ['character' => $character]);
})->name('kanji.detail')->middleware('auth');
