<?php

use Laravel\Socialite\Facades\Socialite;

it('can load login page', function() {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('redirects to twitch to sign in', function() {
    $this->get(route('twitch.login'))->assertRedirectContains("https://id.twitch.tv/oauth2/authorize");
});

it('logs in a user from twitch', function() {
    $socialiteUser = Mockery::mock('Laravel\Socialite\Two\User');

    $socialiteUser->name = 'kyledoesdev';
    $socialiteUser->id = 'abc123';
    $socialiteUser->email = 'kyledoesdev@test.com';
    $socialiteUser->token = 'xyz123';
    $socialiteUser->refreshToken = 'xyz123456';

    Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUser);

    $this->get(route('twitch.process_login'));

    $this->assertDatabaseHas('users', [
        'name' => "kyledoesdev",
        'email'=> "kyledoesdev@test.com",
        'external_id' => "abc123",
        'external_token' => "xyz123",
        'external_refresh_token' => "xyz123456",
    ]);
});
