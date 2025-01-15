<?php

namespace App\Http\Api;

use App\Models\User;
use Exception;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Http;

class Twitch
{
    public function __construct(private User $user)
    {
        $this->refreshToken();
    }

    public function search(string $phrase)
    {
        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->user->external_token,
            'Content-Type' => 'application/json',
            'Client-Id' => config('services.twitch.client_id'),
        ])->get("https://api.twitch.tv/helix/search/categories", [
            'query' => $phrase,
            'first' => 1
        ]);
    }

    private function refreshToken(): void
    {
        try {
            $response = Http::asForm()->post('https://id.twitch.tv/oauth2/token', [
                'client_id' => config('services.twitch.client_id'),
                'client_secret' => config('services.twitch.client_secret'),
                'refresh_token' => $this->user->external_refresh_token,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
        
                $this->user->update(['external_token' => $data['access_token']]);
            }
        } catch(Exception $e) {
            Log::error("could not refresh user: {$this->user->name} token.");
            Log::error($e->getMessage());
        }
    }
}