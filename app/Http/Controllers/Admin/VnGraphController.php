<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VnChoice;
use App\Models\VnDialogue;
use App\Models\VnScene;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // 🔥 WAJIB DITAMBAHKAN UNTUK MENGHAPUS AUDIO
use Inertia\Inertia;

class VnGraphController extends Controller
{
    /**
     * Show the graph editor page for a scene.
     */
    public function show(VnScene $scene)
    {
        $dialogues = $scene->dialogues()
            ->with(['character:id,name', 'background:id,name', 'choices'])
            ->get();

        $nodes = $dialogues->map(function (VnDialogue $d, int $index) {
            return [
                'id' => (string) $d->id,
                'type' => 'dialogue',
                'position' => [
                    'x' => $d->position_x ?: $index * 300,
                    'y' => $d->position_y ?: $index * 100,
                ],
                'data' => [
                    'dialogueId' => $d->id,
                    'characterId' => $d->character_id,
                    'characterName' => $d->character?->name ?? 'Narrator',
                    'backgroundId' => $d->background_id,
                    'backgroundName' => $d->background?->name ?? '',
                    'originalText' => $d->original_text,
                    'translatedText' => $d->translated_text,
                    
                    // 🔥 PERBAIKAN: Kirim path audio ke Vue!
                    'audioFilePath' => $d->audio_file_path, 
                    
                    'choices' => $d->choices->map(fn($c) => [
                        'id' => $c->id,
                        'choiceText' => $c->choice_text,
                        'targetDialogueId' => $c->target_dialogue_id,
                    ])->values()->toArray(),
                ],
            ];
        })->values()->toArray();

        // Build edges from next_dialogue_id and choices
        $edges = [];
        foreach ($dialogues as $d) {
            if ($d->next_dialogue_id) {
                $edges[] = [
                    'id' => "next-{$d->id}-{$d->next_dialogue_id}",
                    'source' => (string) $d->id,
                    'target' => (string) $d->next_dialogue_id,
                    'sourceHandle' => 'next',
                    'type' => 'smoothstep',
                    'animated' => true,
                    'style' => ['stroke' => '#6366f1'],
                    'label' => 'Next',
                ];
            }
            foreach ($d->choices as $choice) {
                $edges[] = [
                    'id' => "choice-{$d->id}-{$choice->id}",
                    'source' => (string) $d->id,
                    'target' => (string) $choice->target_dialogue_id,
                    'sourceHandle' => "choice-{$choice->id}",
                    'type' => 'smoothstep',
                    'animated' => false,
                    'style' => ['stroke' => '#a855f7', 'strokeDasharray' => '5 5'],
                    'label' => $choice->choice_text,
                ];
            }
        }

        return Inertia::render('Admin/Dialogues/GraphEditor', [
            'scene' => $scene,
            'graphData' => [
                'nodes' => $nodes,
                'edges' => $edges,
            ],
            'characters' => $scene->characters()->select('id', 'name')->get(),
            'backgrounds' => $scene->backgrounds()->select('id', 'name')->get(),
            'firstDialogueId' => $scene->first_dialogue_id,
        ]);
    }

    /**
     * Save the entire graph state for a scene.
     */
    public function save(Request $request, VnScene $scene)
    {
        $request->validate([
            'nodes' => 'required|array',
            'nodes.*.id' => 'required',
            'nodes.*.position' => 'required|array',
            'nodes.*.position.x' => 'required|numeric',
            'nodes.*.position.y' => 'required|numeric',
            'nodes.*.data' => 'required|array',
            'nodes.*.data.characterId' => 'nullable|integer',
            'nodes.*.data.backgroundId' => 'nullable|integer',
            'nodes.*.data.originalText' => 'required|string',
            'nodes.*.data.translatedText' => 'required|string',
            'edges' => 'nullable|array',
            'deletedNodeIds' => 'nullable|array',
            'firstDialogueId' => 'nullable',
        ]);

        DB::transaction(function () use ($request, $scene) {
            // PERBAIKAN: Delete removed nodes DAN audionya
            if (!empty($request->deletedNodeIds)) {
                // Ambil data dialognya dulu sebelum dihapus
                $dialoguesToDelete = VnDialogue::whereIn('id', $request->deletedNodeIds)
                    ->where('scene_id', $scene->id)
                    ->get();

                foreach ($dialoguesToDelete as $dialogue) {
                    // Cek apakah ada file audio
                    if ($dialogue->audio_file_path) {
                        // Bersihkan URL/path agar cocok dengan storage public
                        $path = str_replace(['/storage/', 'storage/'], '', $dialogue->audio_file_path);
                        
                        // Hapus file fisik jika ada
                        if (Storage::disk('public')->exists($path)) {
                            Storage::disk('public')->delete($path);
                        }
                    }
                    
                    // Hapus data dari database
                    $dialogue->delete();
                }
            }

            // Upsert nodes (create new ones, update existing)
            $idMap = []; // maps temp ids (negative) to real DB ids
            foreach ($request->nodes as $node) {
                $data = [
                    'scene_id' => $scene->id,
                    'character_id' => $node['data']['characterId'] ?: null,
                    'background_id' => $node['data']['backgroundId'] ?: null,
                    'original_text' => $node['data']['originalText'],
                    'translated_text' => $node['data']['translatedText'],
                    'position_x' => $node['position']['x'],
                    'position_y' => $node['position']['y'],
                    'next_dialogue_id' => null, // will be set by edges
                ];

                $nodeId = $node['id'];
                if (str_starts_with($nodeId, 'new-')) {
                    // New node
                    $dialogue = VnDialogue::create($data);
                    $idMap[$nodeId] = $dialogue->id;
                } else {
                    // Existing node
                    $dialogue = VnDialogue::where('id', $nodeId)
                        ->where('scene_id', $scene->id)
                        ->first();
                    if ($dialogue) {
                        $dialogue->update($data);
                    }
                    $idMap[$nodeId] = (int) $nodeId;
                }
            }

            // Clear all existing choices for this scene's dialogues
            VnChoice::whereIn('dialogue_id', function ($q) use ($scene) {
                $q->select('id')->from('vn_dialogues')->where('scene_id', $scene->id);
            })->delete();

            // Reset all next_dialogue_id
            VnDialogue::where('scene_id', $scene->id)->update(['next_dialogue_id' => null]);

            // Process edges
            foreach ($request->edges ?? [] as $edge) {
                $sourceId = $idMap[$edge['source']] ?? null;
                $targetId = $idMap[$edge['target']] ?? null;

                if (!$sourceId || !$targetId) continue;

                $sourceHandle = $edge['sourceHandle'] ?? 'next';

                if ($sourceHandle === 'next') {
                    // Linear flow
                    VnDialogue::where('id', $sourceId)->update(['next_dialogue_id' => $targetId]);
                } else {
                    // Choice edge
                    $label = $edge['label'] ?? 'Choice';
                    VnChoice::create([
                        'dialogue_id' => $sourceId,
                        'choice_text' => $label,
                        'target_dialogue_id' => $targetId,
                    ]);
                }
            }

            // Update first dialogue
            $firstId = $request->firstDialogueId;
            if ($firstId && isset($idMap[$firstId])) {
                $scene->update(['first_dialogue_id' => $idMap[$firstId]]);
            } elseif ($firstId && is_numeric($firstId)) {
                $scene->update(['first_dialogue_id' => $firstId]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Graph saved successfully.']);
    }
}