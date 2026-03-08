<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VnCharacter;
use App\Models\VnScene;
use App\Services\ElevenLabsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VnCharacterController extends Controller
{
    public function __construct(
        protected ElevenLabsService $elevenLabsService
    ) {}

    public function create(VnScene $scene)
    {
        return Inertia::render('Admin/Characters/Form', [
            'scene' => $scene,
            'character' => null,
            'voices' => $this->elevenLabsService->getAvailableVoices(),
        ]);
    }

    public function store(Request $request, VnScene $scene)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sprite_image' => 'nullable|image|max:5120',
            'elevenlabs_voice_id' => 'nullable|string|max:255',
        ]);

        $data = collect($validated)->except('sprite_image')->toArray();

        if ($request->hasFile('sprite_image')) {
            $data['default_sprite_path'] = $request->file('sprite_image')->store('vn/sprites', 'public');
        }

        $scene->characters()->create($data);

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Character created successfully.');
    }

    public function edit(VnScene $scene, VnCharacter $character)
    {
        return Inertia::render('Admin/Characters/Form', [
            'scene' => $scene,
            'character' => $character,
            'voices' => $this->elevenLabsService->getAvailableVoices(),
        ]);
    }

    public function update(Request $request, VnScene $scene, VnCharacter $character)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sprite_image' => 'nullable|image|max:5120',
            'elevenlabs_voice_id' => 'nullable|string|max:255',
        ]);

        $data = collect($validated)->except('sprite_image')->toArray();

        if ($request->hasFile('sprite_image')) {
            // Delete old sprite if exists
            if ($character->default_sprite_path) {
                Storage::disk('public')->delete($character->default_sprite_path);
            }
            $data['default_sprite_path'] = $request->file('sprite_image')->store('vn/sprites', 'public');
        }

        $character->update($data);

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Character updated successfully.');
    }

    public function destroy(VnScene $scene, VnCharacter $character)
    {
        $character->delete();

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Character deleted successfully.');
    }
}
