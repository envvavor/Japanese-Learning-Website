<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MateriController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\Admin\VnCharacterController;
use App\Http\Controllers\Admin\VnBackgroundController;
use App\Http\Controllers\Admin\VnDialogueController;
use App\Http\Controllers\Admin\VnSceneController;
use App\Http\Controllers\Admin\VnGraphController;
use App\Services\ElevenLabsService;
use App\Http\Controllers\DatasetController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\Admin\AdminQuizController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\QuizNewController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\VocabularyController;
use App\Http\Controllers\Admin\AdminVocabularyController;
use Illuminate\Support\Facades\Http;



// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/dashboard', function () {
    $user = Auth::user();
    $userId = $user->id;
    
    $quizCount = \App\Models\QuizAttempt::where('user_id', $userId)->count()
               + \App\Models\QuizSession::where('user_id', $userId)
                    ->whereNotNull('completed_at')->count();
                    
    $masteredCount = \App\Models\QuizQuestion::whereHas('quizSession', fn($q) => 
                        $q->where('user_id', $userId))
                    ->where('is_correct', true)
                    ->distinct('kanji_id')
                    ->count('kanji_id');

    // MENGHITUNG STREAK
    // Ambil semua tanggal dari QuizAttempt
    $attemptDates = \App\Models\QuizAttempt::where('user_id', $userId)
        ->whereNotNull('completed_at')
        ->pluck('completed_at')
        ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString());

    // Ambil semua tanggal dari QuizSession
    $sessionDates = \App\Models\QuizSession::where('user_id', $userId)
        ->whereNotNull('completed_at')
        ->pluck('completed_at')
        ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString());

    // Gabungkan, hapus duplikat, dan urutkan dari terbaru ke terlama (Descending)
    $dates = $attemptDates->merge($sessionDates)
        ->filter()
        ->unique()
        ->sortDesc()
        ->values()
        ->toArray();

    $streak = 0;

    if (!empty($dates)) {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // Streak hidup JIKA rekor terbarunya adalah "Hari Ini" ATAU "Kemarin"
        if ($dates[0] === $today || $dates[0] === $yesterday) {
            $streak = 1;
            $checkDate = \Carbon\Carbon::parse($dates[0]);

            // Cek mundur ke belakang untuk melihat berapa hari beruntun
            for ($i = 1; $i < count($dates); $i++) {
                if ($dates[$i] === $checkDate->copy()->subDay()->toDateString()) {
                    $streak++;
                    $checkDate->subDay(); // Mundurkan target cek 1 hari
                } else {
                    break; // Streak putus!
                }
            }
        }
    }

    return view('dashboard', compact('quizCount', 'masteredCount', 'streak'));
})->name('dashboard')->middleware('auth');

Route::post('/onboarding/complete', function () {
    Auth::user()->update(['has_seen_onboarding' => true]);
    return response()->json(['success' => true]);
})->name('onboarding.complete')->middleware('auth');

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
            $totalUsers = \App\Models\User::count();

            $labels = [];
            $attemptData = [];
            $sessionData = [];

            for ($i = 29; $i >= 0; $i--) {
                $date = \Carbon\Carbon::today()->subDays($i);
                $labels[] = $date->format('d M');
                $attemptData[] = \App\Models\QuizAttempt::whereDate('created_at', $date)->count();
                $sessionData[] = \App\Models\QuizSession::whereDate('created_at', $date)->count();
            }

            // Calculate Grade Distribution for Pie Chart
            $grades = ['S' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
            
            $allScores = [];
            
            // Kuis Manual
            foreach (\App\Models\QuizAttempt::all() as $attempt) {
                $allScores[] = $attempt->score;
            }
            
            // Kuis AI
            foreach (\App\Models\QuizSession::all() as $session) {
                $allScores[] = $session->score;
            }

            foreach ($allScores as $score) {
                if ($score >= 90) $grades['S']++;
                elseif ($score >= 80) $grades['A']++;
                elseif ($score >= 70) $grades['B']++;
                elseif ($score >= 60) $grades['C']++;
                elseif ($score >= 50) $grades['D']++;
                else $grades['F']++;
            }
            // Format for chart.js
            $gradeLabels = array_keys($grades);
            $gradeData = array_values($grades);

            return view('admin.dashboard', compact('totalKanji', 'totalHiragana', 'totalKatakana', 'totalMateri', 'totalUsers', 'labels', 'attemptData', 'sessionData', 'gradeLabels', 'gradeData'));
        })->name('dashboard');
        
        Route::resource('kanjis', \App\Http\Controllers\Admin\AdminKanjiController::class);

        Route::resource('vocabularies', AdminVocabularyController::class);

        // Materi (Lesson/Article) routes
        Route::post('materis/upload-image', [MateriController::class, 'uploadImage'])->name('materis.uploadImage');
        Route::resource('materis', MateriController::class);

        // User Management routes
        Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
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


// Visual Novel Engine Routes
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

            // Graph Editor
            Route::get('graph', [VnGraphController::class, 'show'])->name('graph');
            Route::post('graph/save', [VnGraphController::class, 'save'])->name('graph.save');
        });
    });

    Route::post('/admin/vn/dialogues/{dialogue}/generate-audio', [VnDialogueController::class, 'generateAudio'])
    ->name('admin.vn.dialogues.generate-audio');

Route::get('/admin/api/elevenlabs-quota', function (ElevenLabsService $elevenLabs) {
    return response()->json($elevenLabs->remainingTokens());
})->name('admin.elevenlabs.quota');

Route::prefix('quiz')->name('quiz.')->middleware('auth')->group(function () {
    Route::get('/', [QuizController::class, 'index'])->name('index');
    Route::post('/start', [QuizController::class, 'start'])->name('start');
    Route::post('/answer', [QuizController::class, 'answer'])->name('answer');
    Route::post('/finish', [QuizController::class, 'finish'])->name('finish');
    Route::get('/history', [QuizController::class, 'history'])->name('history');
    Route::get('/voices', [QuizController::class, 'voices'])->name('voices');
    Route::post('/generate-audio', [QuizController::class, 'generateAudio'])->name('generate-audio');
    Route::post('/import-audio', [QuizController::class, 'importAudio'])->name('import-audio');
    Route::get('/play', function () {
        return view('quiz.play');
    })->name('play');
});

Route::prefix('admin/dataset')->name('admin.dataset.')->middleware(['auth'])->group(function () {
    Route::get('/', [DatasetController::class, 'index'])->name('index');
    Route::delete('/destroy', [DatasetController::class, 'destroy'])->name('destroy');
    Route::get('/download-all', [DatasetController::class, 'downloadAllZip'])->name('download.all');
    Route::get('/download/{character}', [DatasetController::class, 'downloadZip'])->name('download');
});


Route::middleware(['auth', 'admin'])
    ->prefix('admin/quizzes')
    ->name('admin.quizzes.')
    ->group(function () {
        Route::get('/', [AdminQuizController::class, 'index'])->name('index');
        Route::get('/create', [AdminQuizController::class, 'create'])->name('create');
        Route::post('/', [AdminQuizController::class, 'store'])->name('store');
        Route::get('/{quiz}', [AdminQuizController::class, 'show'])->name('show');
        Route::get('/{quiz}/edit', [AdminQuizController::class, 'edit'])->name('edit');
        Route::put('/{quiz}', [AdminQuizController::class, 'update'])->name('update');
        Route::delete('/{quiz}', [AdminQuizController::class, 'destroy'])->name('destroy');

        // Quiz Items (Soal)
        Route::get('/{quiz}/items/create', [AdminQuizController::class, 'createItem'])->name('items.create');
        Route::post('/{quiz}/items', [AdminQuizController::class, 'storeItem'])->name('items.store');
        Route::get('/{quiz}/items/{item}/edit', [AdminQuizController::class, 'editItem'])->name('items.edit');
        Route::put('/{quiz}/items/{item}', [AdminQuizController::class, 'updateItem'])->name('items.update');
        Route::delete('/{quiz}/items/{item}', [AdminQuizController::class, 'destroyItem'])->name('items.destroy');

        // Audio generation via ElevenLabs
        Route::post('/{quiz}/items/{item}/generate-audio', [AdminQuizController::class, 'generateAudio'])->name('items.generate-audio');
        Route::post('/{quiz}/items/{item}/regenerate-audio', [AdminQuizController::class, 'regenerateAudio'])->name('items.regenerate-audio');

        // Kanji search API for drawing autocomplete
        Route::get('/api/search-kanjis', [AdminQuizController::class, 'searchKanjis'])->name('api.search-kanjis');

        // Audio preview (during item creation, no item ID yet)
        Route::post('/api/generate-audio-preview', [AdminQuizController::class, 'generateAudioPreview'])->name('api.generate-audio-preview');
        Route::post('/api/regenerate-audio-preview', [AdminQuizController::class, 'regenerateAudioPreview'])->name('api.regenerate-audio-preview');
    });

Route::middleware('auth')
    ->prefix('quizzes')
    ->name('quizzes.')
    ->group(function () {
        Route::get('/', [QuizNewController::class, 'index'])->name('index');
        Route::get('/{quiz}', [QuizNewController::class, 'show'])->name('show');
        Route::post('/{quiz}/answer', [QuizNewController::class, 'submitAnswer'])->name('answer');
        Route::post('/{quiz}/finish', [QuizNewController::class, 'finishAttempt'])->name('finish');
    });

    // ── Auth routes ──────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Google OAuth ─────────────────────────────────────────────────
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ── Email Verification ───────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [AuthController::class, 'showVerificationNotice'])->name('verification.notice');
    Route::post('/email/verify', [AuthController::class, 'verifyCode'])->name('verification.verify');
    Route::post('/email/resend', [AuthController::class, 'resendCode'])->name('verification.resend');
});

// ── Password Reset ───────────────────────────────────────────────
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('guest');

// ── Profile ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('password.update.profile');
});

Route::get('/kosakata', [VocabularyController::class, 'index'])->name('vocabulary.index');
Route::get('/kosakata/search', [VocabularyController::class, 'search'])->name('vocabulary.search');

Route::get('/api/cnn-status', function () {
    // Ambil IP AWS dari .env
    $awsUrl = env('AI_PREDICT_URL') . '/status';
    
    // Laravel nge-hit server Python/Flask AWS
    $response = Http::get($awsUrl);
    
    // Kembalikan hasilnya ke Frontend
    return $response->json();
});