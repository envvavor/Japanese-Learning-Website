<?php

namespace Database\Seeders;

use App\Models\VnBackground;
use App\Models\VnCharacter;
use App\Models\VnChoice;
use App\Models\VnDialogue;
use App\Models\VnScene;
use Illuminate\Database\Seeder;

class VisualNovelSeeder extends Seeder
{
    public function run(): void
    {
        // ── Scene ──────────────────────────────────
        $scene = VnScene::create([
            'title' => 'Chapter 1: Hari Pertama',
            'description' => 'Semester baru dimulai. Pilih jalanmu di hari pertama sekolah!',
        ]);

        // ── Characters ─────────────────────────────
        $sakura = VnCharacter::create([
            'scene_id' => $scene->id,
            'name' => 'Sakura',
        ]);

        $sensei = VnCharacter::create([
            'scene_id' => $scene->id,
            'name' => 'Tanaka-sensei',
        ]);

        // ── Backgrounds ────────────────────────────
        $classroom = VnBackground::create([
            'scene_id' => $scene->id,
            'name' => 'Classroom',
            'image_path' => 'vn/backgrounds/classroom.jpg',
        ]);

        $hallway = VnBackground::create([
            'scene_id' => $scene->id,
            'name' => 'School Hallway',
            'image_path' => 'vn/backgrounds/hallway.jpg',
        ]);

        // ── Dialogues (story order) ────────────────
        $opening = VnDialogue::create([
            'scene_id' => $scene->id,
            'character_id' => null,
            'background_id' => $classroom->id,
            'original_text' => '新しい学期が始まった。教室に入ると、田中先生が待っていた。',
            'translated_text' => 'Semester baru telah dimulai. Saat masuk kelas, Tanaka-sensei sudah menunggu.',
        ]);

        $choicePoint = VnDialogue::create([
            'scene_id' => $scene->id,
            'character_id' => $sakura->id,
            'background_id' => $classroom->id,
            'original_text' => 'どうしますか？',
            'translated_text' => 'Apa yang akan kamu lakukan?',
        ]);

        $branchA = VnDialogue::create([
            'scene_id' => $scene->id,
            'character_id' => $sensei->id,
            'background_id' => $classroom->id,
            'original_text' => 'いいですね。漢字の練習を始めましょう。',
            'translated_text' => 'Bagus. Mari kita mulai latihan kanji.',
        ]);

        $branchAEnd = VnDialogue::create([
            'scene_id' => $scene->id,
            'character_id' => $sakura->id,
            'background_id' => $classroom->id,
            'original_text' => 'よかった！一緒に勉強しましょう！',
            'translated_text' => 'Syukurlah! Ayo belajar bersama!',
        ]);

        $branchBEnd = VnDialogue::create([
            'scene_id' => $scene->id,
            'character_id' => $sensei->id,
            'background_id' => $hallway->id,
            'original_text' => 'わかりました。また明日会いましょう。',
            'translated_text' => 'Baiklah. Sampai jumpa besok.',
        ]);

        // ── Link dialogues ─────────────────────────
        $opening->update(['next_dialogue_id' => $choicePoint->id]);
        $branchA->update(['next_dialogue_id' => $branchAEnd->id]);

        // Set scene start
        $scene->update(['first_dialogue_id' => $opening->id]);

        // ── Choices ────────────────────────────────
        VnChoice::create([
            'dialogue_id' => $choicePoint->id,
            'choice_text' => '勉強する (Belajar)',
            'target_dialogue_id' => $branchA->id,
        ]);

        VnChoice::create([
            'dialogue_id' => $choicePoint->id,
            'choice_text' => '帰る (Pulang)',
            'target_dialogue_id' => $branchBEnd->id,
        ]);
    }
}
