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
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

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
        $this->registerHealthChecks();
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

    private function registerHealthChecks(): void
    {
        Health::checks([
            EnvironmentCheck::new(),
            DatabaseCheck::new(),
            DatabaseConnectionCountCheck::new()
                ->failWhenMoreConnectionsThan(100),
            DebugModeCheck::new(),
            OptimizedAppCheck::new(),
            ScheduleCheck::new()
                ->heartbeatMaxAgeInMinutes(15),
            UsedDiskSpaceCheck::new()
                ->warnWhenUsedSpaceIsAbovePercentage(90)
                ->failWhenUsedSpaceIsAbovePercentage(95)
        ]);
    }
}
