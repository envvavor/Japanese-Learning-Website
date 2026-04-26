<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizItem;
use App\Models\Kanji;
use App\Services\ElevenLabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminQuizController extends Controller
{
    protected ElevenLabsService $elevenLabs;

    public function __construct(ElevenLabsService $elevenLabs)
    {
        $this->elevenLabs = $elevenLabs;
    }

    /* ---------------------------------------------------------------
     *  QUIZ CRUD
     * --------------------------------------------------------------- */

    public function index()
    {
        $quizzes = Quiz::withCount('items')
            ->orderBy('order')
            ->get();

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $nextOrder = Quiz::max('order') + 1;
        return view('admin.quizzes.create', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'required|integer|min:1',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Quiz::create($validated);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil dibuat!');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load(['items.kanji']);
        $voices = $this->elevenLabs->getAvailableVoices();

        return view('admin.quizzes.show', compact('quiz', 'voices'));
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'required|integer|min:1',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $quiz->update($validated);

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz berhasil diperbarui!');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz berhasil dihapus!');
    }

    /* ---------------------------------------------------------------
     *  QUIZ ITEMS (Soal)
     * --------------------------------------------------------------- */

    public function createItem(Quiz $quiz)
    {
        // Load all kanjis that have strokes (for drawing questions)
        $kanjisWithStrokes = Kanji::whereNotNull('strokes')
            ->whereRaw("JSON_LENGTH(strokes) > 0")
            ->orderBy('category')
            ->orderBy('character')
            ->get(['id', 'character', 'meaning', 'category']);

        $voices = $this->elevenLabs->getAvailableVoices();
        $nextOrder = $quiz->items()->max('order') + 1;

        return view('admin.quizzes.items.create', compact('quiz', 'kanjisWithStrokes', 'voices', 'nextOrder'));
    }

    public function storeItem(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_type'  => 'required|in:multiple_choice,drawing,listening',
            'question_text'  => 'required|string',
            'correct_answer' => 'required|string',
            'options'        => 'nullable|array',
            'options.*'      => 'nullable|string',
            'audio_url'      => 'nullable|string',
            'kanji_id'       => 'nullable|exists:kanjis,id',
            'order'          => 'required|integer|min:1',
        ]);

        // Clean up options for MC & Listening
        if (in_array($validated['question_type'], ['multiple_choice', 'listening'])) {
            $options = array_values(array_filter($validated['options'] ?? [], fn($o) => !empty($o)));
            $validated['options'] = $options ?: null;
        } else {
            $validated['options'] = null;
        }

        // Drawing has no options
        if ($validated['question_type'] === 'drawing') {
            $validated['options'] = null;
        }

        $quiz->items()->create($validated);

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function editItem(Quiz $quiz, QuizItem $item)
    {
        $kanjisWithStrokes = Kanji::whereNotNull('strokes')
            ->whereRaw("JSON_LENGTH(strokes) > 0")
            ->orderBy('category')
            ->orderBy('character')
            ->get(['id', 'character', 'meaning', 'category']);

        $voices = $this->elevenLabs->getAvailableVoices();

        return view('admin.quizzes.items.edit', compact('quiz', 'item', 'kanjisWithStrokes', 'voices'));
    }

    public function updateItem(Request $request, Quiz $quiz, QuizItem $item)
    {
        $validated = $request->validate([
            'question_type'  => 'required|in:multiple_choice,drawing,listening',
            'question_text'  => 'required|string',
            'correct_answer' => 'required|string',
            'options'        => 'nullable|array',
            'options.*'      => 'nullable|string',
            'audio_url'      => 'nullable|string',
            'kanji_id'       => 'nullable|exists:kanjis,id',
            'order'          => 'required|integer|min:1',
        ]);

        if (in_array($validated['question_type'], ['multiple_choice', 'listening'])) {
            $options = array_values(array_filter($validated['options'] ?? [], fn($o) => !empty($o)));
            $validated['options'] = $options ?: null;
        } else {
            $validated['options'] = null;
        }

        if ($validated['question_type'] === 'drawing') {
            $validated['options'] = null;
        }

        $item->update($validated);

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroyItem(Quiz $quiz, QuizItem $item)
    {
        // Delete audio file if exists
        if ($item->audio_url) {
            $path = str_replace('/storage/', '', $item->audio_url);
            Storage::disk('public')->delete($path);
        }

        $item->delete();

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Soal berhasil dihapus!');
    }

    /* ---------------------------------------------------------------
     *  AUDIO GENERATION (ElevenLabs)
     * --------------------------------------------------------------- */

    /**
     * Generate audio for a quiz item (listening question).
     */
    public function generateAudio(Request $request, Quiz $quiz, QuizItem $item)
    {
        $validated = $request->validate([
            'text'     => 'required|string|max:500',
            'voice_id' => 'nullable|string',
        ]);

        $voiceId = $validated['voice_id'];
        if (empty($voiceId)) {
            $voices  = $this->elevenLabs->getAvailableVoices();
            $voiceId = $voices->first()['voice_id'] ?? null;
        }

        if (!$voiceId) {
            return response()->json(['error' => 'Tidak ada voice tersedia.'], 422);
        }

        $audioUrl = $this->elevenLabs->getOrGenerateAudio($validated['text'], $voiceId);

        if (!$audioUrl) {
            return response()->json(['error' => 'Gagal generate audio. Cek API key ElevenLabs.'], 422);
        }

        // Save audio_url to quiz item
        $item->update(['audio_url' => $audioUrl]);

        return response()->json(['audio_url' => $audioUrl]);
    }

    /**
     * Regenerate audio — delete cached file then generate fresh.
     */
    public function regenerateAudio(Request $request, Quiz $quiz, QuizItem $item)
    {
        $validated = $request->validate([
            'text'     => 'required|string|max:500',
            'voice_id' => 'nullable|string',
        ]);

        $voiceId = $validated['voice_id'];
        if (empty($voiceId)) {
            $voices  = $this->elevenLabs->getAvailableVoices();
            $voiceId = $voices->first()['voice_id'] ?? null;
        }

        if (!$voiceId) {
            return response()->json(['error' => 'Tidak ada voice tersedia.'], 422);
        }

        // Delete existing cached file
        if ($item->audio_url) {
            $path = ltrim(str_replace('/storage', '', parse_url($item->audio_url, PHP_URL_PATH)), '/');
            Storage::disk('public')->delete($path);
        }

        // Also delete the md5-based cache if it exists
        $hash     = md5($validated['text'] . $voiceId);
        $filename = "audio/{$hash}.mp3";
        Storage::disk('public')->delete($filename);

        // Generate fresh
        $audioUrl = $this->elevenLabs->getOrGenerateAudio($validated['text'], $voiceId);

        if (!$audioUrl) {
            return response()->json(['error' => 'Gagal regenerate audio. Cek API key ElevenLabs.'], 422);
        }

        $item->update(['audio_url' => $audioUrl]);

        return response()->json(['audio_url' => $audioUrl]);
    }

    /**
     * Generate audio preview (used during item CREATION before item exists).
     */
    public function generateAudioPreview(Request $request)
    {
        $validated = $request->validate([
            'text'     => 'required|string|max:500',
            'voice_id' => 'nullable|string',
        ]);

        $voiceId = $validated['voice_id'];
        if (empty($voiceId)) {
            $voices  = $this->elevenLabs->getAvailableVoices();
            $voiceId = $voices->first()['voice_id'] ?? null;
        }

        if (!$voiceId) {
            return response()->json(['error' => 'Tidak ada voice tersedia.'], 422);
        }

        $audioUrl = $this->elevenLabs->getOrGenerateAudio($validated['text'], $voiceId);

        if (!$audioUrl) {
            return response()->json(['error' => 'Gagal generate audio. Cek API key ElevenLabs.'], 422);
        }

        return response()->json(['audio_url' => $audioUrl]);
    }

    /**
     * Regenerate audio preview (delete cache then generate fresh).
     */
    public function regenerateAudioPreview(Request $request)
    {
        $validated = $request->validate([
            'text'        => 'required|string|max:500',
            'voice_id'    => 'nullable|string',
            'current_url' => 'nullable|string',
        ]);

        $voiceId = $validated['voice_id'];
        if (empty($voiceId)) {
            $voices  = $this->elevenLabs->getAvailableVoices();
            $voiceId = $voices->first()['voice_id'] ?? null;
        }

        if (!$voiceId) {
            return response()->json(['error' => 'Tidak ada voice tersedia.'], 422);
        }

        // Delete cached file
        $hash     = md5($validated['text'] . $voiceId);
        $filename = "audio/{$hash}.mp3";
        Storage::disk('public')->delete($filename);

        $audioUrl = $this->elevenLabs->getOrGenerateAudio($validated['text'], $voiceId);

        if (!$audioUrl) {
            return response()->json(['error' => 'Gagal regenerate audio.'], 422);
        }

        return response()->json(['audio_url' => $audioUrl]);
    }

    /**
     * API: search kanjis that have strokes (for drawing autocomplete).
     */
    public function searchKanjis(Request $request)
    {
        $q = $request->input('q', '');

        $kanjis = Kanji::whereNotNull('strokes')
            ->whereRaw("JSON_LENGTH(strokes) > 0")
            ->where(function ($query) use ($q) {
                $query->where('character', 'like', "%{$q}%")
                    ->orWhere('meaning', 'like', "%{$q}%");
            })
            ->orderBy('category')
            ->limit(20)
            ->get(['id', 'character', 'meaning', 'category', 'strokes']);

        return response()->json($kanjis);
    }
}
