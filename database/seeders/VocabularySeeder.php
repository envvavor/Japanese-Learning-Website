<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VocabularySeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('seeders/jlpt_vocab.csv');

        if (!file_exists($csvPath)) {
            $this->command->error('File tidak ditemukan: ' . $csvPath);
            $this->command->info('Pastikan file jlpt_vocab.csv ada di folder database/seeders/');
            return;
        }

        $this->command->info('Mulai import kosakata JLPT...');

        // Hapus data lama jika ada
        DB::table('vocabularies')->truncate();

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // Skip header row
        
        $batch    = [];
        $count    = 0;
        $batchSize = 500;
        $now      = now()->toDateTimeString();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) continue;

            $level = strtoupper(trim($row[3]));

            // Validasi level
            if (!in_array($level, ['N1', 'N2', 'N3', 'N4', 'N5'])) continue;

            $batch[] = [
                'original'   => trim($row[0]),
                'furigana'   => trim($row[1]) ?: null,
                'english'    => trim($row[2]),
                'jlpt_level' => $level,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;

            // Insert per batch agar tidak habis memory
            if (count($batch) >= $batchSize) {
                DB::table('vocabularies')->insert($batch);
                $batch = [];
                $this->command->info("  {$count} baris diimport...");
            }
        }

        // Insert sisa batch
        if (!empty($batch)) {
            DB::table('vocabularies')->insert($batch);
        }

        fclose($handle);

        $this->command->info("✅ Selesai! Total {$count} kosakata berhasil diimport.");
    }
}