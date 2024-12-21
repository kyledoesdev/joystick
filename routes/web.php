<?php

use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\FeedMiddleware;
use App\Livewire\Dashboard;
use App\Livewire\Feed\Feed;
use App\Livewire\Group\CreateGroup;
use App\Livewire\Group\EditGroup;
use App\Livewire\Group\Lists;
use Illuminate\Support\Facades\Route;

Route::get('/', WelcomeController::class)->name('welcome');

Route::get('/login/twitch', [SocialiteController::class, 'login'])->name('twitch.login');
Route::get('/login/twitch/callback', [SocialiteController::class, 'processLogin'])->name('twitch.process_login');

Route::middleware(['auth'])->group(function() {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    /* Groups */
    Route::get('/group/create', CreateGroup::class)->name('group.create');
    Route::get('/group/{id}/edit', EditGroup::class)->name('group.edit');

    Route::get('/group/{id}/lists', Lists::class)->name('group.lists');
    
    Route::get('/list/{id}/feed', Feed::class)->middleware(FeedMiddleware::class)->name('feed');
});