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
                'allDialogues' => [],
                'startDialogueId' => null,
            ]);
        }

        return redirect()->route('vn.play', $firstDialogue->id);
    }

    /**
     * Play a specific dialogue scene.
     * Auto-generate audio untuk semua dialog di scene ini, lalu kirim ke Vue.
     */
    public function play(VnDialogue $dialogue)
    {
        $sceneId = $dialogue->scene_id;

        // 1. Ambil SEMUA dialog di scene ini
        $allDialoguesInScene = VnDialogue::with(['character', 'background', 'choices'])
            ->where('scene_id', $sceneId)
            ->get();

        // 2. 🔥 AUTO-GENERATE AUDIO (ElevenLabs) UNTUK SEMUA DIALOG YANG KOSONG
        foreach ($allDialoguesInScene as $d) {
            if (
                !$d->audio_file_path
                && $d->character_id
                && $d->character
                && $d->character->elevenlabs_voice_id
            ) {
                // Generate TTS audio
                $audioUrl = $this->elevenLabsService->getOrGenerateAudio(
                    $d->original_text,
                    $d->character->elevenlabs_voice_id
                );

                if ($audioUrl) {
                    // Simpan ke database agar request berikutnya tidak generate lagi
                    $d->update(['audio_file_path' => $audioUrl]);
                    $d->audio_file_path = $audioUrl; 
                }
            }
        }

        // 3. Format seluruh data tersebut menjadi array agar siap digunakan oleh Vue
        $formattedDialogues = $allDialoguesInScene->map(function ($d) {
            return [
                'id' => $d->id,
                'original_text' => $d->original_text,
                'translated_text' => $d->translated_text,
                'audio_file_path' => $d->audio_file_path, // Sekarang pasti terisi kalau karakternya punya voice_id
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

        // 4. Kirim semua data ke Vue Player
        return Inertia::render('Game/Play', [
            'allDialogues' => $formattedDialogues,
            'startDialogueId' => $dialogue->id,
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