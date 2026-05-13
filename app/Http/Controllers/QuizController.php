<?php

namespace App\Http\Controllers;

use App\Models\Kanji;
use App\Models\QuizSession;
use App\Models\QuizQuestion;
use App\Services\ElevenLabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    protected ElevenLabsService $elevenLabs;

    public function __construct(ElevenLabsService $elevenLabs)
    {
        $this->elevenLabs = $elevenLabs;
    }

    /**
     * Show quiz setup page.
     */
    public function index()
    {
        $recentSessions = QuizSession::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('quiz.index', [
            'recentSessions' => $recentSessions,
        ]);
    }

    /**
     * Start a new quiz session.
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'category' => 'nullable|string',
            'quiz_type' => 'required|in:multiple_choice,drawing,listening,mixed',
            'question_count' => 'required|in:5,10,15,20',
        ]);

        $category = $validated['category'] ?? null;
        $quizType = $validated['quiz_type'];
        $questionCount = (int) $validated['question_count'];

        // Fetch random kanjis filtered by category
        $kanjiQuery = Kanji::query();
        if ($category) {
            $kanjiQuery->where('category', $category);
        }
        $kanjis = $kanjiQuery->inRandomOrder()->take($questionCount)->get();

        if ($kanjis->count() < $questionCount) {
            return response()->json([
                'error' => "Tidak cukup karakter tersedia. Hanya ada {$kanjis->count()} karakter" .
                    ($category ? " untuk kategori '{$category}'" : '') . "."
            ], 422);
        }

        // Get first available voice for listening questions
        $firstVoiceId = null;
        if (in_array($quizType, ['listening', 'mixed'])) {
            $voices = $this->elevenLabs->getAvailableVoices();
            $firstVoiceId = $voices->first()['voice_id'] ?? null;
        }

        // Get all kanjis for generating wrong options
        $allKanjis = Kanji::all();

        // Create session
        $session = QuizSession::create([
            'user_id' => Auth::id(),
            'category' => $category,
            'quiz_type' => $quizType,
            'total_questions' => $questionCount,
            'correct_answers' => 0,
            'score' => 0,
        ]);

        $questions = [];

        foreach ($kanjis as $kanji) {
            $assignedType = $quizType;

            if ($quizType === 'mixed') {
                // Random assign with weights: 40% MC, 30% drawing, 30% listening
                $rand = mt_rand(1, 100);
                if ($rand <= 40) {
                    $assignedType = 'multiple_choice';
                } elseif ($rand <= 70) {
                    $assignedType = 'drawing';
                } else {
                    $assignedType = 'listening';
                }

                // Skip drawing if no strokes, reassign to MC
                if ($assignedType === 'drawing' && (empty($kanji->strokes) || !is_array($kanji->strokes) || count($kanji->strokes) === 0)) {
                    $assignedType = 'multiple_choice';
                }
            }

            $questionData = $this->generateQuestion($kanji, $assignedType, $allKanjis, $category, $firstVoiceId);

            if ($questionData === null) {
                // Fallback to multiple_choice if question generation fails
                $questionData = $this->generateQuestion($kanji, 'multiple_choice', $allKanjis, $category, $firstVoiceId);
            }

            $questionData['session_id'] = $session->id;
            $questionData['kanji_id'] = $kanji->id;

            $question = QuizQuestion::create($questionData);

            $questions[] = [
                'id' => $question->id,
                'question_type' => $question->question_type,
                'question_subtype' => $question->question_subtype,
                'question_text' => $question->question_text,
                'character' => $kanji->character,
                'meaning' => $kanji->meaning,
                'kunyomi' => $kanji->kunyomi,
                'onyomi' => $kanji->onyomi,
                'options' => $question->options,
                'audio_url' => $question->audio_url,
                'strokes' => $kanji->strokes,
                'stroke_order_image' => $kanji->stroke_order_image ? '/storage/' . $kanji->stroke_order_image : null,
            ];
        }

        return response()->json([
            'session_id' => $session->id,
            'questions' => $questions,
        ]);
    }

    /**
     * Generate a single question based on type.
     */
    private function generateQuestion(Kanji $kanji, string $type, $allKanjis, ?string $category, ?string $firstVoiceId): ?array
    {
        switch ($type) {
            case 'multiple_choice':
                return $this->generateMultipleChoice($kanji, $allKanjis, $category);
            case 'drawing':
                return $this->generateDrawing($kanji);
            case 'listening':
                return $this->generateListening($kanji, $allKanjis, $category, $firstVoiceId);
            default:
                return $this->generateMultipleChoice($kanji, $allKanjis, $category);
        }
    }

    /**
     * Generate a multiple choice question.
     */
    private function generateMultipleChoice(Kanji $kanji, $allKanjis, ?string $category): array
    {
        // 50% chance: ask for meaning or ask for character
        $askMeaning = mt_rand(0, 1) === 0;

        if ($askMeaning) {
            $questionText = "Apa arti dari karakter 「{$kanji->character}」?";
            $correctAnswer = $kanji->meaning;

            // Get wrong options (meanings)
            $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'meaning');
            $options = array_merge([$correctAnswer], $wrongOptions);
        } else {
            $questionText = "Pilih karakter yang berarti '{$kanji->meaning}':";
            $correctAnswer = $kanji->character;

            // Get wrong options (characters)
            $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'character');
            $options = array_merge([$correctAnswer], $wrongOptions);
        }

        shuffle($options);

        return [
            'question_type' => 'multiple_choice',
            'question_subtype' => null,
            'question_text' => $questionText,
            'correct_answer' => $correctAnswer,
            'options' => $options,
            'audio_url' => null,
        ];
    }

    /**
     * Generate a drawing question.
     */
    private function generateDrawing(Kanji $kanji): ?array
    {
        if (empty($kanji->strokes) || !is_array($kanji->strokes) || count($kanji->strokes) === 0) {
            return null; // Skip if no strokes
        }

        return [
            'question_type' => 'drawing',
            'question_subtype' => null,
            'question_text' => "Tulis karakter 「{$kanji->character}」 ({$kanji->meaning}) dengan benar!",
            'correct_answer' => $kanji->character,
            'options' => null,
            'audio_url' => null,
        ];
    }

    /**
     * Generate a listening question.
     */
    private function generateListening(Kanji $kanji, $allKanjis, ?string $category, ?string $voiceId): array
    {
        // Randomly assign subtype
        $subtypes = ['listen_to_meaning', 'listen_to_character', 'read_and_listen', 'listen_to_reading'];
        $subtype = $subtypes[array_rand($subtypes)];

        // If kunyomi is null, force subtype to listen_to_meaning
        if (empty($kanji->kunyomi)) {
            if ($subtype === 'listen_to_reading') {
                $subtype = 'listen_to_meaning';
            }
        }

        // Generate audio
        $audioUrl = null;
        if ($voiceId) {
            $audioUrl = $this->elevenLabs->getOrGenerateAudio($kanji->character, $voiceId);
        }

        // Set question text, correct answer, and options based on subtype
        switch ($subtype) {
            case 'listen_to_meaning':
                $questionText = "Apa arti dari yang diucapkan?";
                $correctAnswer = $kanji->meaning;
                $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'meaning');
                $options = array_merge([$correctAnswer], $wrongOptions);
                break;

            case 'listen_to_character':
                $questionText = "Pilih karakter yang sesuai dengan audio:";
                $correctAnswer = $kanji->character;
                $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'character');
                $options = array_merge([$correctAnswer], $wrongOptions);
                break;

            case 'read_and_listen':
                $questionText = "Apa arti dari 「{$kanji->character}」 ini?";
                $correctAnswer = $kanji->meaning;
                $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'meaning');
                $options = array_merge([$correctAnswer], $wrongOptions);
                break;

            case 'listen_to_reading':
                $questionText = "Bagaimana cara membaca karakter ini?";
                $correctAnswer = $kanji->kunyomi;
                $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'kunyomi');
                $options = array_merge([$correctAnswer], $wrongOptions);
                break;

            default:
                $questionText = "Apa arti dari yang diucapkan?";
                $correctAnswer = $kanji->meaning;
                $wrongOptions = $this->getWrongOptions($kanji, $allKanjis, $category, 'meaning');
                $options = array_merge([$correctAnswer], $wrongOptions);
                break;
        }

        shuffle($options);

        return [
            'question_type' => 'listening',
            'question_subtype' => $subtype,
            'question_text' => $questionText,
            'correct_answer' => $correctAnswer,
            'options' => $options,
            'audio_url' => $audioUrl,
        ];
    }

    /**
     * Get 3 wrong options from other kanjis.
     */
    private function getWrongOptions(Kanji $kanji, $allKanjis, ?string $category, string $field): array
    {
        // First try same category
        $sameCategory = $allKanjis->where('id', '!=', $kanji->id);
        if ($category) {
            $sameCategoryFiltered = $sameCategory->where('category', $category);
            if ($sameCategoryFiltered->count() >= 3) {
                $sameCategory = $sameCategoryFiltered;
            }
            // If not enough in same category, use all categories
        }

        // Filter out nulls for the requested field
        $candidates = $sameCategory->filter(function ($k) use ($field) {
            return !empty($k->{$field});
        });

        $wrongOptions = $candidates->random(min(3, $candidates->count()))
            ->pluck($field)
            ->unique()
            ->values()
            ->take(3)
            ->toArray();

        // If still not enough, add generic fallback
        while (count($wrongOptions) < 3) {
            $wrongOptions[] = '???';
        }

        return $wrongOptions;
    }

    /**
     * Submit an answer for a question.
     */
    public function answer(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'user_answer' => 'required|string',
            'accuracy_score' => 'nullable|numeric|min:0|max:100',
            'time_taken_seconds' => 'nullable|integer|min:0',
            'text_was_revealed' => 'nullable|boolean',
            'hint_was_used' => 'nullable|boolean',
        ]);

        $question = QuizQuestion::with('kanji.examples')->findOrFail($validated['question_id']);

        // Ensure question belongs to auth user's session
        if ($question->quizSession->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userAnswer = $validated['user_answer'];
        $accuracyScore = $validated['accuracy_score'] ?? null;
        $timeTaken = $validated['time_taken_seconds'] ?? null;
        $textRevealed = $validated['text_was_revealed'] ?? false;
        $hintUsed = $validated['hint_was_used'] ?? false;

        // Determine is_correct
        if ($question->question_type === 'drawing') {
            $isCorrect = $accuracyScore !== null && $accuracyScore >= 75;
        } else {
            $isCorrect = $userAnswer === $question->correct_answer;
        }

        // Calculate points_earned
        $pointsEarned = 0;
        if ($isCorrect) {
            if ($hintUsed || $textRevealed) {
                $pointsEarned = 1;
            } else {
                if ($question->question_type === 'drawing') {
                    $pointsEarned = 2;
                } else {
                    if ($timeTaken !== null && $timeTaken <= 5) {
                        $pointsEarned = 3;
                    } elseif ($timeTaken !== null && $timeTaken <= 15) {
                        $pointsEarned = 2;
                    } else {
                        $pointsEarned = 1;
                    }
                }
            }
        }

        // Update question
        $question->update([
            'user_answer' => $userAnswer,
            'is_correct' => $isCorrect,
            'accuracy_score' => $accuracyScore,
            'time_taken_seconds' => $timeTaken,
            'text_was_revealed' => $textRevealed,
            'hint_was_used' => $hintUsed,
            'points_earned' => $pointsEarned,
        ]);

        // Get explanation from kanji examples
        $explanation = null;
        $firstExample = $question->kanji->examples->first();
        if ($firstExample) {
            $explanation = [
                'japanese_text' => $firstExample->japanese_text,
                'furigana_html' => $firstExample->furigana_html,
                'meaning' => $firstExample->meaning,
            ];
        }

        return response()->json([
            'is_correct' => $isCorrect,
            'correct_answer' => $question->correct_answer,
            'points_earned' => $pointsEarned,
            'explanation' => $explanation,
        ]);
    }

    /**
     * Finish a quiz session.
     */
    public function finish(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|exists:quiz_sessions,id',
        ]);

        $session = QuizSession::with(['quizQuestions.kanji.examples'])->findOrFail($validated['session_id']);

        // Ensure session belongs to auth user
        if ($session->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $questions = $session->quizQuestions;
        $correctCount = $questions->where('is_correct', true)->count();
        $total = $session->total_questions;
        $score = $total > 0 ? round(($correctCount / $total) * 100, 2) : 0;
        $textRevealedCount = $questions->where('text_was_revealed', true)->count();

        $session->update([
            'correct_answers' => $correctCount,
            'score' => $score,
            'questions_with_text_revealed' => $textRevealedCount,
            'completed_at' => now(),
        ]);

        // Refresh to get updated values
        $session->refresh();

        $totalPoints = $questions->sum('points_earned');
        $xpEarned = $totalPoints * 3;

        if ($xpEarned > 0) {
            Auth::user()->addXp($xpEarned);
        }

        $results = $questions->map(function ($q) {
            return [
                'question_id' => $q->id,
                'question_type' => $q->question_type,
                'question_subtype' => $q->question_subtype,
                'character' => $q->kanji->character,
                'meaning' => $q->kanji->meaning,
                'user_answer' => $q->user_answer,
                'correct_answer' => $q->correct_answer,
                'is_correct' => $q->is_correct,
                'accuracy_score' => $q->accuracy_score,
                'points_earned' => $q->points_earned,
                'time_taken_seconds' => $q->time_taken_seconds,
                'hint_was_used' => $q->hint_was_used,
                'text_was_revealed' => $q->text_was_revealed,
                'kunyomi' => $q->kanji->kunyomi,
                'onyomi' => $q->kanji->onyomi,
                'stroke_order_image' => $q->kanji->stroke_order_image ? '/storage/' . $q->kanji->stroke_order_image : null,
                'examples' => $q->kanji->examples->map(function ($ex) {
                    return [
                        'japanese_text' => $ex->japanese_text,
                        'furigana_html' => $ex->furigana_html,
                        'meaning' => $ex->meaning,
                    ];
                })->toArray(),
            ];
        });

        return response()->json([
            'score' => $score,
            'correct' => $correctCount,
            'total' => $total,
            'passed' => $session->passed,
            'grade' => $session->grade,
            'questions_with_text_revealed' => $textRevealedCount,
            'total_points' => $questions->sum('points_earned'),
            'results' => $results,
        ]);
    }

    /**
     * Show quiz history.
     */
    public function history()
    {
        $sessions = QuizSession::where('user_id', Auth::id())
            ->whereNotNull('completed_at')
            ->orderBy('created_at', 'desc')
            ->withCount('quizQuestions')
            ->paginate(10);

        return view('quiz.history', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * Generate audio via ElevenLabs.
     */
    public function generateAudio(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:200',
            'voice_id' => 'nullable|string',
        ]);

        $voiceId = $validated['voice_id'];

        if (empty($voiceId)) {
            $voices = $this->elevenLabs->getAvailableVoices();
            $firstVoice = $voices->first();
            $voiceId = $firstVoice['voice_id'] ?? null;

            if (!$voiceId) {
                return response()->json(['error' => 'Tidak ada voice yang tersedia.'], 422);
            }
        }

        $audioUrl = $this->elevenLabs->getOrGenerateAudio($validated['text'], $voiceId);

        if (!$audioUrl) {
            return response()->json(['error' => 'Gagal menghasilkan audio. Silakan coba lagi.'], 422);
        }

        return response()->json(['audio_url' => $audioUrl]);
    }

    /**
     * Import audio file.
     */
    public function importAudio(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'audio' => 'required|file|mimes:mp3,wav,ogg|max:10240',
        ]);

        $question = QuizQuestion::findOrFail($validated['question_id']);

        // Ensure question belongs to auth user's session
        if ($question->quizSession->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $file = $request->file('audio');
        $ext = $file->getClientOriginalExtension();
        $filename = 'audio/imported/' . uniqid() . '.' . $ext;

        Storage::disk('public')->put($filename, file_get_contents($file));

        $audioUrl = '/storage/' . $filename;

        $question->update(['audio_url' => $audioUrl]);

        return response()->json(['audio_url' => $audioUrl]);
    }

    /**
     * Get available voices and quota.
     */
    public function voices()
    {
        $voices = $this->elevenLabs->getAvailableVoices();
        $quota = $this->elevenLabs->remainingTokens();

        return response()->json([
            'voices' => $voices,
            'quota' => $quota,
        ]);
    }
}
