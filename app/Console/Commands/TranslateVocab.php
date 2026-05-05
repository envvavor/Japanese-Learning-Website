<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateVocab extends Command
{
    protected $signature = 'vocab:translate';
    protected $description = 'Menerjemahkan vocab perlahan tanpa Proxy';

    public function handle()
    {
        $translator = new GoogleTranslate('id');
        $translator->setSource('en');

        $this->info("Memulai proses terjemahan 200k data (Mode Aman)...");

        // Ambil data perlahan, 50 kata per kelompok
        DB::table('vocabularies')->whereNull('indonesian')->chunkById(200, function ($vocabs) use ($translator) {
            
            foreach ($vocabs as $vocab) {
                try {
                    $terjemahan = $translator->translate($vocab->english);
                    
                    DB::table('vocabularies')
                        ->where('id', $vocab->id)
                        ->update(['indonesian' => $terjemahan]);

                    $this->line("Sukses [ID: {$vocab->id}]: {$vocab->english} -> {$terjemahan}");
                    
                    // Jeda aman 1 detik per kata (1000000 microsecond)
                    usleep(200000); 

                } catch (\Exception $e) {
                    $this->error("Gagal [ID: {$vocab->id}]: " . $e->getMessage());
                    
                    // Jika kena limit Google, istirahat 10 detik sebelum lanjut ke kata berikutnya
                    sleep(10); 
                }
            }
            
            // Ganti sleep(5) menjadi:
            $this->info("Selesai 200 kata. Istirahat 5 detik...");
            sleep(5);
        });

        $this->info("Selesai menerjemahkan!");
    }
}