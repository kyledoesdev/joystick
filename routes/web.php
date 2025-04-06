<?php

use App\Http\Controllers\SocialiteController;
use App\Http\Middleware\GroupMiddleware;
use App\Http\Middleware\IsDeveloper;
use App\Livewire\Dashboard;
use App\Livewire\Group\EditGroup;
use App\Livewire\Group\GroupFeeds;
use App\Livewire\Group\ShowGroup;
use App\Livewire\Invites\Index;
use App\Livewire\Suggestions\Show as Suggestions;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\HealthCheckResultsController;

Route::get('/', fn () => auth()->check() ? redirect(route('dashboard')) : view('welcome'))->name('login');

Route::get('/login/twitch', [SocialiteController::class, 'login'])->name('twitch.login');
Route::get('/login/twitch/callback', [SocialiteController::class, 'processLogin'])->name('twitch.process_login');

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    /* Groups */
    Route::get('/group/{group}/edit', EditGroup::class)->name('group.edit');

    Route::middleware(GroupMiddleware::class)->group(function() {
        Route::get('/group/{group}', ShowGroup::class)->name('group');
        Route::get('/group/{group}/feed/{feed}', Suggestions::class)->name('feed');
    });

    Route::get('/invites', Index::class)->name('invites');

    Route::middleware(IsDeveloper::class)->group(function() {
        Route::get('health', HealthCheckResultsController::class);
    });
});