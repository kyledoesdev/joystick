<?php

use App\Http\Api\Twitch;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function() {
    $this->user = Mockery::mock(User::class);
    $this->user->shouldReceive('getAttribute')->with('external_token')->andReturn('fake-token');
    $this->user->shouldReceive('getAttribute')->with('external_refresh_token')->andReturn('fake-refresh-token');

    Http::preventStrayRequests();
});

test('can search for games', function() {    
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'access_token' => 'new-fake-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
        ], 200),
        'https://api.twitch.tv/helix/search/categories*' => Http::response([
            'data' => [
                [
                    'id' => '123',
                    'name' => 'Factorio'
                ]
            ]
        ], 200),
    ]);
    
    $this->user->shouldReceive('update')
        ->with(['external_token' => 'new-fake-token'])
        ->once();
    
    $twitch = new Twitch($this->user);
    
    $response = $twitch->search('Factorio');

    expect($response->json('data')[0]['name'])->toBe('Factorio');
});

test('refreshes twitch token for a user', function() {
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'access_token' => 'new-fake-token',
            'refresh_token' => 'new-refresh-token',
            'expires_in' => 3600,
        ], 200),
    ]);
    
    $this->user->shouldReceive('update')
        ->with(['external_token' => 'new-fake-token'])
        ->once();
    
    $twitch = new Twitch($this->user);
    
    Http::assertSent(function ($request) {
        return $request->url() === 'https://id.twitch.tv/oauth2/token' &&
            $request['client_id'] === config('services.twitch.client_id') &&
            $request['client_secret'] === config('services.twitch.client_secret') &&
            $request['refresh_token'] === 'fake-refresh-token' &&
            $request['grant_type'] === 'refresh_token';
    });
});

test('handles failed token refresh', function() {
    Http::fake([
        'https://id.twitch.tv/oauth2/token' => Http::response([
            'message' => 'Invalid refresh token',
            'status' => 500
        ], 500),
    ]);
    
    $this->user->shouldNotReceive('update');
    
    $twitch = new Twitch($this->user);
    
    Http::assertSent(function ($request) {
        return $request->url() === 'https://id.twitch.tv/oauth2/token' &&
            $request['client_id'] === config('services.twitch.client_id') &&
            $request['client_secret'] === config('services.twitch.client_secret') &&
            $request['refresh_token'] === 'fake-refresh-token' &&
            $request['grant_type'] === 'refresh_token';
    });
});