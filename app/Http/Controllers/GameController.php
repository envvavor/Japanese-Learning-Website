<?php

namespace App\Http\Controllers;

use App\Models\VnDialogue;
use App\Models\VnScene;
use App\Services\ElevenLabsService;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class GameController extends Controller
{
    public function __construct(
        protected ElevenLabsService $elevenLabsService
    ) {}

    /**
     * Start playing the visual novel from the entry dialogue.
     * Finds the "root" dialogue — one that is not referenced as a
     * next_dialogue_id or as a choice target_dialogue_id.
     */
    public function start()
    {
        // Collect all dialogue IDs that are targets of something else
        $referencedIds = VnDialogue::whereNotNull('next_dialogue_id')
            ->pluck('next_dialogue_id')
            ->merge(
                \App\Models\VnChoice::pluck('target_dialogue_id')
            )
            ->unique();

        // The root dialogue is the one not referenced by anything
        $firstDialogue = VnDialogue::whereNotIn('id', $referencedIds)
            ->orderBy('id')
            ->first();

        // Fallback: just grab the first one by ID
        if (!$firstDialogue) {
            $firstDialogue = VnDialogue::orderBy('id')->first();
        }

        if (!$firstDialogue) {
            return Inertia::render('Game/Play', [
                'dialogue' => null,
            ]);
        }

        return redirect()->route('vn.play', $firstDialogue->id);
    }

    /**
     * Play a specific dialogue.
     */
    public function play(VnDialogue $dialogue)
    {
        $dialogue->load(['character', 'background', 'choices']);

        // Generate TTS audio if needed
        if (
            !$dialogue->audio_file_path
            && $dialogue->character_id
            && $dialogue->character
            && $dialogue->character->elevenlabs_voice_id
        ) {
            $audioUrl = $this->elevenLabsService->getOrGenerateAudio(
                $dialogue->original_text,
                $dialogue->character->elevenlabs_voice_id
            );

            if ($audioUrl) {
                $dialogue->update(['audio_file_path' => $audioUrl]);
                $dialogue->audio_file_path = $audioUrl;
            }
        }

        return Inertia::render('Game/Play', [
            'dialogue' => [
                'id' => $dialogue->id,
                'original_text' => $dialogue->original_text,
                'translated_text' => $dialogue->translated_text,
                'audio_file_path' => $dialogue->audio_file_path,
                'next_dialogue_id' => $dialogue->next_dialogue_id,
                'character' => $dialogue->character ? [
                    'name' => $dialogue->character->name,
                    'default_sprite_path' => $dialogue->character->default_sprite_path
                        ? Storage::url($dialogue->character->default_sprite_path)
                        : null,
                ] : null,
                'background' => [
                    'name' => $dialogue->background->name,
                    'image_url' => Storage::url($dialogue->background->image_path),
                ],
                'choices' => $dialogue->choices->map(fn ($choice) => [
                    'id' => $choice->id,
                    'choice_text' => $choice->choice_text,
                    'target_dialogue_id' => $choice->target_dialogue_id,
                ]),
            ],
        ]);
    }

    /**
     * Show the list of available VN scenes for the user.
     */
    public function scenes()
    {
        $scenes = VnScene::whereNotNull('first_dialogue_id')->latest()->get();
        return view('vn-scenes', compact('scenes'));
    }
}
