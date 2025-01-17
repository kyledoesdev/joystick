<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller 
{
    public function login() 
    {
        return Socialite::driver('twitch')->redirect();
    }

    public function processLogin()
    {
        $user = Socialite::driver('twitch')->stateless()->user();

        $deletedUser = User::withTrashed()
            ->where('external_id', $user->id)
            ->whereNotNull('deleted_at')
            ->first();

        if (!is_null($deletedUser)) {
            $deletedUser->restore();
        }

        $user = User::withTrashed()->updateOrCreate([
            'external_id' => $user->id,
        ], [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar ?? "https://api.dicebear.com/7.x/initials/svg?seed={$user->name}",
            'external_token' => $user->token,
            'external_refresh_token' => $user->refreshToken,
            'timezone' => get_timezone(),
            'ip_address' => request()->ip() ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'user_platform' => $_SERVER['HTTP_SEC_CH_UA_PLATFORM'] ?? '',
        ]);

        Log::channel('debug_discord')->warning($user->email . ' just logged in!!');

        Auth::login($user, true);

        return redirect(route('dashboard'));
    }
}