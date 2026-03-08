<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VnDialogue;
use App\Models\VnScene;
use Illuminate\Http\Request;
use Inertia\Inertia;

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

        $dialogue->delete();

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Dialogue deleted successfully.');
    }
}
