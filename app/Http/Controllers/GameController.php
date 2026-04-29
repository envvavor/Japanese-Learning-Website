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

    public function start()
    {
        $referencedIds = VnDialogue::whereNotNull('next_dialogue_id')
            ->pluck('next_dialogue_id')
            ->merge(
                \App\Models\VnChoice::pluck('target_dialogue_id')
            )
            ->unique();

        $firstDialogue = VnDialogue::whereNotIn('id', $referencedIds)
            ->orderBy('id')
            ->first();

        if (!$firstDialogue) {
            $firstDialogue = VnDialogue::orderBy('id')->first();
        }

        if (!$firstDialogue) {
            return Inertia::render('Game/Play', [
                'allDialogues' => [],
                'startDialogueId' => null,
                'sceneId' => null,
                'sceneVersion' => null,
            ]);
        }

        return redirect()->route('vn.play', $firstDialogue->id);
    }

    public function play(VnDialogue $dialogue)
    {
        $sceneId = $dialogue->scene_id;

        // Ambil data scene untuk versioning
        $scene = VnScene::find($sceneId);

        $allDialoguesInScene = VnDialogue::with(['character', 'background', 'choices'])
            ->where('scene_id', $sceneId)
            ->get();

        foreach ($allDialoguesInScene as $d) {
            if (
                !$d->audio_file_path
                && $d->character_id
                && $d->character
                && $d->character->elevenlabs_voice_id
            ) {
                $audioUrl = $this->elevenLabsService->getOrGenerateAudio(
                    $d->original_text,
                    $d->character->elevenlabs_voice_id
                );

                if ($audioUrl) {
                    $d->update(['audio_file_path' => $audioUrl]);
                    $d->audio_file_path = $audioUrl;
                }
            }
        }

        $formattedDialogues = $allDialoguesInScene->map(function ($d) {
            return [
                'id' => $d->id,
                'original_text' => $d->original_text,
                'translated_text' => $d->translated_text,
                'audio_file_path' => $d->audio_file_path,
                'next_dialogue_id' => $d->next_dialogue_id,
                'character' => $d->character ? [
                    'name' => $d->character->name,
                    'default_sprite_path' => $d->character->default_sprite_path
                        ? Storage::url($d->character->default_sprite_path)
                        : null,
                ] : null,
                'background' => $d->background ? [
                    'name' => $d->background->name,
                    'image_url' => Storage::url($d->background->image_path),
                ] : null,
                'choices' => $d->choices->map(fn ($choice) => [
                    'id' => $choice->id,
                    'choice_text' => $choice->choice_text,
                    'target_dialogue_id' => $choice->target_dialogue_id,
                ]),
            ];
        });

        return Inertia::render('Game/Play', [
            'allDialogues' => $formattedDialogues,
            'startDialogueId' => $dialogue->id,
            // Dikirim ke Vue untuk cache key
            'sceneId' => $sceneId,
            'sceneVersion' => $scene?->updated_at?->timestamp ?? 0,
        ]);
    }

    public function scenes()
    {
        $scenes = VnScene::whereNotNull('first_dialogue_id')->latest()->get();
        return view('vn-scenes', compact('scenes'));
    }
}