<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VnDialogue;
use App\Models\VnCharacter;
use App\Services\ElevenLabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use App\Models\VnScene;

class VnDialogueController extends Controller
{
    public function create(VnScene $scene)
    {
        return Inertia::render('Admin/Dialogues/Form', [
            'scene' => $scene,
            'dialogue' => null,
            'characters' => $scene->characters()->get(),
            'backgrounds' => $scene->backgrounds()->get(),
            'allDialogues' => $scene->dialogues()->select('id', 'original_text')->get(),
        ]);
    }

    public function store(Request $request, VnScene $scene)
    {
        $validated = $request->validate([
            'character_id' => 'nullable|exists:vn_characters,id',
            'background_id' => 'required|exists:vn_backgrounds,id',
            'original_text' => 'required|string',
            'translated_text' => 'required|string',
            'next_dialogue_id' => 'nullable|exists:vn_dialogues,id',
            'is_first' => 'nullable|boolean',
            'choices' => 'nullable|array',
            'choices.*.choice_text' => 'required_with:choices|string|max:255',
            'choices.*.target_dialogue_id' => 'required_with:choices|exists:vn_dialogues,id',
        ]);

        $dialogue = $scene->dialogues()->create([
            'character_id' => $validated['character_id'],
            'background_id' => $validated['background_id'],
            'original_text' => $validated['original_text'],
            'translated_text' => $validated['translated_text'],
            'next_dialogue_id' => $validated['next_dialogue_id'] ?? null,
        ]);

        if (!empty($validated['choices'])) {
            foreach ($validated['choices'] as $choice) {
                $dialogue->choices()->create($choice);
            }
        }

        // Set as first dialogue if requested
        if (!empty($validated['is_first'])) {
            $scene->update(['first_dialogue_id' => $dialogue->id]);
        }

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Dialogue created successfully.');
    }

    public function edit(VnScene $scene, VnDialogue $dialogue)
    {
        $dialogue->load('choices');

        return Inertia::render('Admin/Dialogues/Form', [
            'scene' => $scene,
            'dialogue' => $dialogue,
            'characters' => $scene->characters()->get(),
            'backgrounds' => $scene->backgrounds()->get(),
            'allDialogues' => $scene->dialogues()->select('id', 'original_text')->get(),
            'isFirst' => $scene->first_dialogue_id === $dialogue->id,
        ]);
    }

    public function update(Request $request, VnScene $scene, VnDialogue $dialogue)
    {
        $validated = $request->validate([
            'character_id' => 'nullable|exists:vn_characters,id',
            'background_id' => 'required|exists:vn_backgrounds,id',
            'original_text' => 'required|string',
            'translated_text' => 'required|string',
            'next_dialogue_id' => 'nullable|exists:vn_dialogues,id',
            'is_first' => 'nullable|boolean',
            'choices' => 'nullable|array',
            'choices.*.choice_text' => 'required_with:choices|string|max:255',
            'choices.*.target_dialogue_id' => 'required_with:choices|exists:vn_dialogues,id',
        ]);

        $dialogue->update([
            'character_id' => $validated['character_id'],
            'background_id' => $validated['background_id'],
            'original_text' => $validated['original_text'],
            'translated_text' => $validated['translated_text'],
            'next_dialogue_id' => $validated['next_dialogue_id'] ?? null,
        ]);

        $dialogue->choices()->delete();
        if (!empty($validated['choices'])) {
            foreach ($validated['choices'] as $choice) {
                $dialogue->choices()->create($choice);
            }
        }

        // Update first dialogue
        if (!empty($validated['is_first'])) {
            $scene->update(['first_dialogue_id' => $dialogue->id]);
        } elseif ($scene->first_dialogue_id === $dialogue->id) {
            $scene->update(['first_dialogue_id' => null]);
        }

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Dialogue updated successfully.');
    }

    public function destroy(VnScene $scene, VnDialogue $dialogue)
    {
        if ($scene->first_dialogue_id === $dialogue->id) {
            $scene->update(['first_dialogue_id' => null]);
        }

        // 🔥 LOGIKA HAPUS AUDIO
        if ($dialogue->audio_file_path) {
            $path = str_replace('/storage/', '', $dialogue->audio_file_path);
            Storage::disk('public')->delete($path);
        }

        $dialogue->delete();

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Dialogue and audio deleted successfully.');
    }

    // Load service ElevenLabs ke dalam controller
    protected ElevenLabsService $elevenLabs;

    public function __construct(ElevenLabsService $elevenLabs)
    {
        $this->elevenLabs = $elevenLabs;
    }

    // ... (fungsi store, update, destroy lainnya biarkan saja) ...

    /**
     * 🔥 FUNGSI GENERATE AUDIO DARI VUE GRAPH
     */
    public function generateAudio(Request $request, VnDialogue $dialogue)
    {
        // Validasi Input dari Vue Axios
        $request->validate([
            'text' => 'required|string',
            'character_id' => 'required|exists:vn_characters,id',
        ]);

        // Ambil Voice ID Karakter dari Database
        $character = VnCharacter::find($request->character_id);
        $voiceId = $character->elevenlabs_voice_id;

        if (empty($voiceId)) {
            return response()->json([
                'message' => "Karakter '{$character->name}' belum memiliki Voice ID ElevenLabs."
            ], 400); // 400 Bad Request
        }

        // HAPUS AUDIO LAMA (Sangat Penting!)
        // Karena service-mu pakai MD5 cache, kita harus hapus file lamanya dulu
        // agar sistem dipaksa nge-hit API ElevenLabs lagi (Regenerate sejati).
        if ($dialogue->audio_file_path) {
            $oldPath = str_replace(['/storage/', 'storage/', 'public/', '/public/'], '', $dialogue->audio_file_path);
            $oldPath = ltrim($oldPath, '/');
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // TEMBAK API ELEVENLABS MENGGUNAKAN SERVICE-MU
        // Panggil fungsi yang ada di ElevenLabsService.php
        $audioUrl = $this->elevenLabs->getOrGenerateAudio($request->text, $voiceId);

        // Jika API gagal (kembalian null)
        if (!$audioUrl) {
            return response()->json([
                'message' => 'Gagal men-generate audio. Periksa API Key ElevenLabs atau Kuota Karaktermu.'
            ], 500); // 500 Internal Server Error
        }

        // UPDATE DATABASE DENGAN PATH BARU
        // Fungsi Storage::url() di service-mu otomatis menghasilkan "/storage/audio/xxx.mp3"
        $dialogue->update([
            'audio_file_path' => $audioUrl,
        ]);

        // KIRIM JAWABAN SUKSES KE VUE
        return response()->json([
            'message' => 'Audio berhasil di-generate!',
            'audio_file_path' => $audioUrl
        ]);
    }
}
