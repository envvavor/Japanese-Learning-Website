<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class QuizNewController extends Controller
{
    /**
     * Show list of all quizzes (Duolingo-style, locked/unlocked).
     */
    public function index()
    {
        $userId  = Auth::id();
        $quizzes = Quiz::where('is_active', true)
            ->withCount('items')
            ->orderBy('order')
            ->get();

        // Build lock/unlock status per quiz
        $quizzesWithStatus = $quizzes->map(function ($quiz, $index) use ($userId, $quizzes) {
            $isPassed   = $quiz->isPassedByUser($userId);
            $bestScore  = $quiz->bestScoreForUser($userId);

            // First quiz is always unlocked; subsequent quizzes need previous to be passed
            if ($index === 0) {
                $isLocked = false;
            } else {
                $previousQuiz = $quizzes->get($index - 1);
                $isLocked     = !$previousQuiz->isPassedByUser($userId);
            }

            return [
                'id'          => $quiz->id,
                'title'       => $quiz->title,
                'description' => $quiz->description,
                'order'       => $quiz->order,
                'items_count' => $quiz->items_count,
                'is_locked'   => $isLocked,
                'is_passed'   => $isPassed,
                'best_score'  => $bestScore,
            ];
        });

        return view('quiz-new.index', compact('quizzesWithStatus'));
    }

    /**
     * Show a single quiz (start screen + begin attempt).
     */
    public function show(Request $request, Quiz $quiz)
    {
        $userId = Auth::id();

        // Check if this quiz is accessible
        $quizzes    = Quiz::where('is_active', true)->orderBy('order')->get();
        $quizIndex  = $quizzes->search(fn($q) => $q->id === $quiz->id);

        if ($quizIndex > 0) {
            $previousQuiz = $quizzes->get($quizIndex - 1);
            if (!$previousQuiz->isPassedByUser($userId)) {
                return redirect()->route('quizzes.index')
                    ->with('error', 'Selesaikan quiz sebelumnya dengan sempurna terlebih dahulu!');
            }
        }

        $quiz->load(['items' => function ($q) {
            $q->orderBy('order');
        }, 'items.kanji']);

        $items = $quiz->items;

        // Filter if retry
        if ($request->has('retry')) {
            $retryIds = explode(',', $request->query('retry'));
            $items = $items->filter(function($item) use ($retryIds) {
                return in_array($item->id, $retryIds);
            })->values(); // reset keys
        }

        // Build questions payload for JS
        $questions = $items->map(function ($item) {
            $data = [
                'id'            => $item->id,
                'question_type' => $item->question_type,
                'question_text' => $item->question_text,
                'correct_answer'=> $item->correct_answer,
                'options'       => $item->options,
                'audio_url'     => $item->audio_url,
                'character'     => null,
                'meaning'       => null,
                'strokes'       => null,
                'stroke_order_image' => null,
            ];

            if ($item->kanji) {
                $data['character']          = $item->kanji->character;
                $data['meaning']            = $item->kanji->meaning;
                $data['strokes']            = $item->kanji->strokes;
                $data['stroke_order_image'] = $item->kanji->stroke_order_image
                    ? '/storage/' . $item->kanji->stroke_order_image
                    : null;
            }

            return $data;
        });

        $bestScore = $quiz->bestScoreForUser($userId);

        return view('quiz-new.show', compact('quiz', 'questions', 'bestScore'));
    }

    /**
     * Submit one answer for a quiz item (called per soal via AJAX).
     */
    public function submitAnswer(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'item_id'        => 'required|exists:quiz_items,id',
            'user_answer'    => 'required|string',
            'accuracy_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $item = \App\Models\QuizItem::findOrFail($validated['item_id']);

        // Determine correctness
        if ($item->question_type === 'drawing') {
            $isCorrect = ($validated['accuracy_score'] ?? 0) >= 75;
        } else {
            $userAnswer = $validated['user_answer'];
            $isCorrect = trim($userAnswer) === trim($item->correct_answer);

            if (!$isCorrect && function_exists('normalizer_normalize')) {
                $normUser = \Normalizer::normalize(trim($userAnswer), \Normalizer::FORM_C);
                $normCorrect = \Normalizer::normalize(trim($item->correct_answer), \Normalizer::FORM_C);
                $isCorrect = $normUser === $normCorrect;
            }

            if (!$isCorrect && is_array($item->options) && in_array($userAnswer, $item->options, true)) {
                $isCorrect = mb_strtolower(trim($userAnswer), 'UTF-8') === mb_strtolower(trim($item->correct_answer), 'UTF-8');
            }
        }

        return response()->json([
            'is_correct'     => $isCorrect,
            'correct_answer' => $item->correct_answer,
        ]);
    }

    /**
     * Finish quiz attempt — save score and determine pass/fail.
     */
    public function finishAttempt(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'answers' => 'required|array',
            // Each answer: { item_id, is_correct }
            'answers.*.item_id'   => 'required|integer',
            'answers.*.is_correct'=> 'required|boolean',
        ]);

        $userId          = Auth::id();
        $totalQuestions  = count($validated['answers']);
        $correctAnswers  = collect($validated['answers'])->where('is_correct', true)->count();
        $wrongItemIds    = collect($validated['answers'])->where('is_correct', false)->pluck('item_id')->toArray();
        $score           = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $passed          = ($score >= 100); // Must be perfect

        QuizAttempt::create([
            'user_id'         => $userId,
            'quiz_id'         => $quiz->id,
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctAnswers,
            'score'           => $score,
            'passed'          => $passed,
            'completed_at'    => now(),
        ]);

        $xpEarned = $passed
            ? $totalQuestions * 10
            : $correctAnswers * 2;

        if ($xpEarned > 0) {
            Auth::user()->addXp($xpEarned);
        }

        return response()->json([
            'score'     => $score,
            'passed'    => $passed,
            'correct'   => $correctAnswers,
            'total'     => $totalQuestions,
            'xp_earned' => $xpEarned,
            'new_level' => Auth::user()->level,
            'new_xp'    => Auth::user()->xp,
            'wrong_item_ids' => $wrongItemIds,
        ]);
    }
}
