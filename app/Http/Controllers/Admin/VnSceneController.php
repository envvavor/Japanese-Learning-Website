<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VnDialogue;
use App\Models\VnScene;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VnSceneController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Scenes/Index', [
            'scenes' => VnScene::withCount(['characters', 'backgrounds', 'dialogues'])->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Scenes/Form', [
            'scene' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('vn/thumbnails', 'public');
        }

        VnScene::create($data);

        return redirect()->route('admin.vn.scenes.index')
            ->with('success', 'Scene created successfully.');
    }

    public function show(VnScene $scene)
    {
        $scene->load([
            'characters',
            'backgrounds',
            'dialogues.character',
            'dialogues.background',
            'dialogues.choices',
        ]);

        return Inertia::render('Admin/Scenes/Show', [
            'scene' => $scene,
        ]);
    }

    public function edit(VnScene $scene)
    {
        return Inertia::render('Admin/Scenes/Form', [
            'scene' => $scene,
        ]);
    }

    public function update(Request $request, VnScene $scene)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
        ];

        if ($request->hasFile('thumbnail')) {
            if ($scene->thumbnail_path) {
                Storage::disk('public')->delete($scene->thumbnail_path);
            }
            $data['thumbnail_path'] = $request->file('thumbnail')->store('vn/thumbnails', 'public');
        }

        $scene->update($data);

        return redirect()->route('admin.vn.scenes.index')
            ->with('success', 'Scene updated successfully.');
    }

    public function destroy(VnScene $scene)
    {
        // CATAT SEMUA FILE SEBELUM ADA YANG DIHAPUS DARI DATABASE
        $filesToDelete = [];

        // Catat Thumbnail
        if ($scene->thumbnail_path) {
            $filesToDelete[] = str_replace('/storage/', '', $scene->thumbnail_path);
        }

        // Catat Sprite Karakter
        foreach ($scene->characters()->get() as $char) {
            if ($char->default_sprite_path) {
                $filesToDelete[] = str_replace('/storage/', '', $char->default_sprite_path);
            }
        }

        // Catat Gambar Background
        foreach ($scene->backgrounds()->get() as $bg) {
            if ($bg->image_path) {
                $filesToDelete[] = str_replace('/storage/', '', $bg->image_path);
            }
        }

        // Catat Audio Dialog
        foreach ($scene->dialogues()->get() as $dialogue) {
            if ($dialogue->audio_file_path) {
                $filesToDelete[] = str_replace('/storage/', '', $dialogue->audio_file_path);
            }
        }

        // EKSEKUSI PEMBANTAIAN FILE FISIK (MP3, PNG, JPG)
        foreach ($filesToDelete as $path) {
            Storage::disk('public')->delete($path);
        }

        // EKSEKUSI HAPUS DATABASE (Dari anak ke induk agar tidak error)
        foreach ($scene->dialogues()->get() as $dialogue) {
            $dialogue->choices()->delete(); 
            $dialogue->delete();
        }

        foreach ($scene->characters()->get() as $char) {
            $char->delete();
        }

        foreach ($scene->backgrounds()->get() as $bg) {
            $bg->delete();
        }

        $scene->delete();

        return redirect()->route('admin.vn.scenes.index')
            ->with('success', 'Scene Telah Dihapus');
    }
}
