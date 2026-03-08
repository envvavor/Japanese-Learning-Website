<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VnBackground;
use App\Models\VnScene;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VnBackgroundController extends Controller
{
    public function create(VnScene $scene)
    {
        return Inertia::render('Admin/Backgrounds/Form', [
            'scene' => $scene,
            'background' => null,
        ]);
    }

    public function store(Request $request, VnScene $scene)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:5120',
        ]);

        $path = $request->file('image')->store('vn/backgrounds', 'public');

        $scene->backgrounds()->create([
            'name' => $validated['name'],
            'image_path' => $path,
        ]);

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Background created successfully.');
    }

    public function edit(VnScene $scene, VnBackground $background)
    {
        return Inertia::render('Admin/Backgrounds/Form', [
            'scene' => $scene,
            'background' => $background,
        ]);
    }

    public function update(Request $request, VnScene $scene, VnBackground $background)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = ['name' => $validated['name']];

        if ($request->hasFile('image')) {
            if ($background->image_path) {
                Storage::disk('public')->delete($background->image_path);
            }
            $data['image_path'] = $request->file('image')->store('vn/backgrounds', 'public');
        }

        $background->update($data);

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Background updated successfully.');
    }

    public function destroy(VnScene $scene, VnBackground $background)
    {
        if ($background->image_path) {
            Storage::disk('public')->delete($background->image_path);
        }

        $background->delete();

        return redirect()->route('admin.vn.scenes.show', $scene)
            ->with('success', 'Background deleted successfully.');
    }
}
