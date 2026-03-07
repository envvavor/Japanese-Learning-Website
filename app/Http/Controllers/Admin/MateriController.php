<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    /**
     * Display a listing of all materi.
     */
    public function index()
    {
        $materis = Materi::latest()->paginate(10);
        return view('admin.materis.index', compact('materis'));
    }

    /**
     * Show the form for creating a new materi.
     */
    public function create()
    {
        return view('admin.materis.create');
    }

    /**
     * Store a newly created materi in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Materi::create($validated);

        return redirect()->route('admin.materis.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified materi.
     */
    public function edit(Materi $materi)
    {
        return view('admin.materis.edit', compact('materi'));
    }

    /**
     * Update the specified materi in the database.
     */
    public function update(Request $request, Materi $materi)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $materi->update($validated);

        return redirect()->route('admin.materis.index')->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the specified materi from the database.
     */
    public function destroy(Materi $materi)
    {
        $materi->delete();
        return redirect()->route('admin.materis.index')->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * Handle image upload from TinyMCE editor.
     * Returns JSON with image location for TinyMCE to insert.
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $path = $request->file('file')->store('materi_images', 'public');

        return response()->json([
            'location' => '/storage/' . $path,
        ]);
    }
}
