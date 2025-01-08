<?php

use App\Http\Controllers\SocialiteController;
use App\Http\Middleware\GroupMiddleware;
use App\Livewire\Dashboard;
use App\Livewire\Feed\Show as Feed;
use App\Livewire\Group\Edit;
use App\Livewire\Group\GroupFeeds;
use App\Livewire\Invites\Index;
use App\Livewire\Suggestions\Show as Suggestions;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect(route('dashboard')) : view('login'))->name('login');

Route::get('/login/twitch', [SocialiteController::class, 'login'])->name('twitch.login');
Route::get('/login/twitch/callback', [SocialiteController::class, 'processLogin'])->name('twitch.process_login');

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    /* Groups */
    Route::get('/group/{id}/edit', Edit::class)->name('group.edit');

    Route::middleware(GroupMiddleware::class)->group(function() {
        Route::get('/group/{groupId}', Feed::class)->name('group');
        Route::get('/group/{groupId}/feed/{feedId}', Suggestions::class)->name('feed');
    });

    Route::get('/invites', Index::class)->name('invites');
});