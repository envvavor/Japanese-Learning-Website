<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JmDictSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = database_path('seeders/jmdict-translations-dictionary.json');

        if (!file_exists($jsonPath)) {
            $this->command->error('File tidak ditemukan: ' . $jsonPath);
            $this->command->info('Pastikan file jmdict-translations-dictionary.json ada di folder database/seeders/');
            return;
        }

        $this->command->info('Mulai import kosakata dari JMDict...');
        $this->command->info('Membaca file JSON (ini mungkin butuh waktu)...');

        // Ambil semua kata "original" yang sudah ada di DB (dari JLPT seeder)
        $existingWords = DB::table('vocabularies')
            ->pluck('original')
            ->flip()
            ->toArray();

        $existingCount = count($existingWords);
        $this->command->info("Ditemukan {$existingCount} kata yang sudah ada di database.");

        // Baca JSON file
        $json = file_get_contents($jsonPath);
        $dictionary = json_decode($json, true);

        if ($dictionary === null) {
            $this->command->error('Gagal membaca file JSON. Error: ' . json_last_error_msg());
            return;
        }

        $totalEntries = count($dictionary);
        $this->command->info("Total entri di JMDict: {$totalEntries}");

        $batch     = [];
        $inserted  = 0;
        $skipped   = 0;
        $batchSize = 500;
        $now       = now()->toDateTimeString();

        foreach ($dictionary as $word => $readings) {
            // Skip jika kata sudah ada dari JLPT seeder
            if (isset($existingWords[$word])) {
                $skipped++;
                continue;
            }

            // Ambil reading pertama saja
            // Format: [[reading, [meaning1, meaning2, ...]], ...]
            if (empty($readings) || !is_array($readings)) continue;

            $firstReading = $readings[0];
            $furigana     = $firstReading[0] ?? null;
            $meanings     = $firstReading[1] ?? [];

            // Gabungkan semua meanings jadi satu string
            $english = is_array($meanings) ? implode('; ', $meanings) : (string) $meanings;

            if (empty($english)) continue;

            // Jika ada multiple readings, tambahkan juga meanings dari reading lain
            if (count($readings) > 1) {
                $allMeanings = [];
                foreach ($readings as $reading) {
                    if (isset($reading[1]) && is_array($reading[1])) {
                        foreach ($reading[1] as $m) {
                            $allMeanings[] = $m;
                        }
                    }
                }
                // Deduplicate meanings
                $allMeanings = array_unique($allMeanings);
                $english = implode('; ', $allMeanings);
            }

            $batch[] = [
                'original'   => $word,
                'furigana'   => $furigana,
                'english'    => $english,
                'jlpt_level' => null, // JMDict tidak punya level JLPT
                'created_at' => $now,
                'updated_at' => $now,
            ];

            // Tandai kata ini sudah ditambahkan agar tidak duplikat dalam file JMDict sendiri
            $existingWords[$word] = true;
            $inserted++;

            // Insert per batch
            if (count($batch) >= $batchSize) {
                DB::table('vocabularies')->insert($batch);
                $batch = [];
                $this->command->info("  {$inserted} kata baru diimport, {$skipped} di-skip (duplikat)...");
            }
        }

        // Insert sisa batch
        if (!empty($batch)) {
            DB::table('vocabularies')->insert($batch);
        }

        // Free memory
        unset($dictionary, $json, $existingWords);

        $this->command->info("✅ Selesai! {$inserted} kata baru diimport dari JMDict. {$skipped} kata di-skip karena sudah ada dari JLPT.");
    }
}
