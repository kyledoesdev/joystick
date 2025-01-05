<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Twitch\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('twitch', Provider::class);
        });

        $this->bootMacros();
    }

    private function bootMacros(): void
    {
        Carbon::macro('inUserTimezone', function() {
            return $this->tz(auth()->user()?->timezone ?? 'America/New_York');
        });

        Str::macro('possessive', function ($string) {
            return $string . '\'' . (
                Str::endsWith($string, ['s', 'S']) ? '' : 's'
            );
        });
    }
}
