<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MateriController;

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
            $totalMateri = \App\Models\Materi::count();
            return view('admin.dashboard', compact('totalKanji', 'totalHiragana', 'totalKatakana', 'totalMateri'));
        })->name('dashboard');
        
        Route::resource('kanjis', \App\Http\Controllers\Admin\AdminKanjiController::class);

        // Materi (Lesson/Article) routes
        Route::post('materis/upload-image', [MateriController::class, 'uploadImage'])->name('materis.uploadImage');
        Route::resource('materis', MateriController::class);
    });

Route::get('/list/{category?}', function ($category = null) {
    return view('list', ['category' => $category]);
})->name('list-kanji')->middleware('auth');

// kategori terpisah dengan halaman sendiri
Route::get('/hiragana', function () {
    return view('list', ['category' => 'hiragana']);
})->middleware('auth');
Route::get('/katakana', function () {
    return view('list', ['category' => 'katakana']);
})->middleware('auth');
Route::get('/kanji', function () {
    return view('list', ['category' => 'kanji']);
})->middleware('auth');

// detail kanji untuk melihat informasi
Route::get('/kanji/{character}', function ($character) {
    return view('kanji-detail', ['character' => $character]);
})->name('kanji.detail')->middleware('auth');

// User-facing materi list
Route::get('/materi', function () {
    $materis = \App\Models\Materi::oldest()->paginate(12);
    return view('materis.index', compact('materis'));
})->name('materi.index')->middleware('auth');

// User-facing materi/article view
Route::get('/materi/{materi:slug}', function (\App\Models\Materi $materi) {
    return view('materis.show', compact('materi'));
})->name('materi.show')->middleware('auth');

// =============================================
// Visual Novel Engine Routes
// =============================================
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\VnCharacterController;
use App\Http\Controllers\Admin\VnBackgroundController;
use App\Http\Controllers\Admin\VnDialogueController;
use App\Http\Controllers\Admin\VnSceneController;

// VN Player routes
Route::prefix('vn')->name('vn.')->middleware('auth')->group(function () {
    Route::get('/', [GameController::class, 'start'])->name('start');
    Route::get('/play/{dialogue}', [GameController::class, 'play'])->name('play');
    Route::get('/scenes', [GameController::class, 'scenes'])->name('scenes');
});

// VN Admin routes
Route::middleware(['auth', 'admin'])
    ->prefix('admin/vn')
    ->name('admin.vn.')
    ->group(function () {
        Route::resource('scenes', VnSceneController::class);

        // Nested under scenes
        Route::prefix('scenes/{scene}')->name('scenes.')->group(function () {
            Route::get('characters/create', [VnCharacterController::class, 'create'])->name('characters.create');
            Route::post('characters', [VnCharacterController::class, 'store'])->name('characters.store');
            Route::get('characters/{character}/edit', [VnCharacterController::class, 'edit'])->name('characters.edit');
            Route::put('characters/{character}', [VnCharacterController::class, 'update'])->name('characters.update');
            Route::delete('characters/{character}', [VnCharacterController::class, 'destroy'])->name('characters.destroy');

            Route::get('backgrounds/create', [VnBackgroundController::class, 'create'])->name('backgrounds.create');
            Route::post('backgrounds', [VnBackgroundController::class, 'store'])->name('backgrounds.store');
            Route::get('backgrounds/{background}/edit', [VnBackgroundController::class, 'edit'])->name('backgrounds.edit');
            Route::put('backgrounds/{background}', [VnBackgroundController::class, 'update'])->name('backgrounds.update');
            Route::delete('backgrounds/{background}', [VnBackgroundController::class, 'destroy'])->name('backgrounds.destroy');

            Route::get('dialogues/create', [VnDialogueController::class, 'create'])->name('dialogues.create');
            Route::post('dialogues', [VnDialogueController::class, 'store'])->name('dialogues.store');
            Route::get('dialogues/{dialogue}/edit', [VnDialogueController::class, 'edit'])->name('dialogues.edit');
            Route::put('dialogues/{dialogue}', [VnDialogueController::class, 'update'])->name('dialogues.update');
            Route::delete('dialogues/{dialogue}', [VnDialogueController::class, 'destroy'])->name('dialogues.destroy');
        });
    });
