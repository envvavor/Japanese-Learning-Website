<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class ElevenLabsService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.elevenlabs.io/v1';

    public function __construct()
    {
        $this->apiKey = config('services.elevenlabs.api_key', '');
    }

    /**
     * Get available voices from the ElevenLabs API.
     */
    public function getAvailableVoices(): Collection
    {
        if (empty($this->apiKey) || $this->apiKey === '<your_api_key_here>') {
            return collect();
        }

        try {
            $response = Http::withHeaders([
                'xi-api-key' => $this->apiKey,
            ])->get("{$this->baseUrl}/voices");

            if ($response->successful()) {
                $voices = $response->json('voices', []);
                return collect($voices)->map(function ($voice) {
                    return [
                        'voice_id' => $voice['voice_id'],
                        'name' => $voice['name'],
                    ];
                });
            }

            return collect();
        } catch (\Exception $e) {
            report($e);
            return collect();
        }
    }

    /**
     * Get existing audio or generate new audio via ElevenLabs TTS.
     */
    public function getOrGenerateAudio(string $text, string $voiceId): ?string
    {
        if (empty($this->apiKey) || $this->apiKey === '<your_api_key_here>') {
            return null;
        }

        // Create a unique filename based on text hash and voice ID
        $hash = md5($text . $voiceId);
        $filename = "audio/{$hash}.mp3";

        // Check if the file already exists in local storage
        if (Storage::disk('public')->exists($filename)) {
            return '/storage/' . $filename;
        }

        try {
            $response = Http::withHeaders([
                'xi-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'audio/mpeg',
            ])->post("{$this->baseUrl}/text-to-speech/{$voiceId}", [
                'text' => $text,
                'model_id' => 'eleven_v3',
                'voice_settings' => [
                    'use_speaker_boost' => true
                ],
            ]);

            if ($response->successful()) {
                Storage::disk('public')->put($filename, $response->body());
                return '/storage/' . $filename;
            }

            return null;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }

    public function remainingTokens()
{
    if (empty($this->apiKey) || $this->apiKey === '<your_api_key_here>') {
        return ['remaining' => 0, 'limit' => 0, 'used' => 0];
    }

    try {
        $response = Http::withHeaders([
            'xi-api-key' => $this->apiKey,
        ])->get("{$this->baseUrl}/user/subscription");

        if ($response->successful()) {
            $limit = $response->json('character_limit', 0);
            $used = $response->json('character_count', 0);
            $remaining = max(0, $limit - $used); 
            
            // Return array biar bisa diproses JS
            return [
                'remaining' => $remaining,
                'limit' => $limit,
                'used' => $used
            ];
        }

        return ['remaining' => 0, 'limit' => 0, 'used' => 0];
    } catch (\Exception $e) {
        report($e);
        return ['remaining' => 0, 'limit' => 0, 'used' => 0];
    }
}
}
