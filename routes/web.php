<?php

use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\GroupMiddleware;
use App\Livewire\Dashboard;
use App\Livewire\Feed\Show as FeedShow;
use App\Livewire\Group\CreateGroup;
use App\Livewire\Group\EditGroup;
use App\Livewire\Group\GroupFeeds;
use App\Livewire\Group\Show as GroupShow;
use App\Livewire\Invites\Index;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('welcome');

Route::get('/login/twitch', [SocialiteController::class, 'login'])->name('twitch.login');
Route::get('/login/twitch/callback', [SocialiteController::class, 'processLogin'])->name('twitch.process_login');

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    /* Groups */
    Route::get('/group/create', CreateGroup::class)->name('group.create');
    Route::get('/group/{id}/edit', EditGroup::class)->name('group.edit');

    Route::middleware(GroupMiddleware::class)->group(function() {
        Route::get('/group/{groupId}', GroupShow::class)->name('group');
        Route::get('/group/{groupId}/feed/{feedId}', FeedShow::class)->name('feed');
    });

    Route::get('/invites', Index::class)->name('invites');
});