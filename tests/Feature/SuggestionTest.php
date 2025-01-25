<?php

use App\Livewire\Games\Search;
use App\Livewire\Suggestions\Create;
use App\Livewire\Suggestions\Edit;
use App\Livewire\Suggestions\Show;
use App\Models\Game;
use App\Models\Group;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\Vote;

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->group = Group::factory()->withOwner($this->user)->create();
    $this->feed = $this->group->feeds->first();

    /* using default backlog feed for suggestions - which we test in the GroupTest that it is created automatically. */
});

test('can load suggestions for a feed', function() {
    $this->actingAs($this->user)
        ->get(route('feed', ['group' => $this->group, 'feed' => $this->feed]))
        ->assertOk();
});

test('can not load suggestions for a feed when the user is not in the group', function() {
    $this->actingAs(User::factory()->create())
        ->get(route('feed', ['group' => $this->group, 'feed' => $this->feed]))
        ->assertForbidden();
});

test('creating a suggestion receives a game search event', function() {
    Livewire::test(Create::class)
        ->dispatch('game-searched', [
            'id' => '123',
            'name' => 'Factorio',
            'box_art_url' => fix_box_art('https://static-cdn.jtvnw.net/ttv-boxart/130942_IGDB-52x72.jpg')
        ])
        ->assertSet('searchedGame', [
            'id' => '123',
            'name' => 'Factorio',
            'box_art_url' => fix_box_art('https://static-cdn.jtvnw.net/ttv-boxart/130942_IGDB-52x72.jpg')
        ]);
});

test('clearing a searched game resets the searched game', function() {
    Livewire::actingAs($this->user)
        ->test(Search::class)
        ->dispatch('game-search-cleared')
        ->assertSet('phrase', '');
});

test('user\'s uggestion is created with an auto up vote', function () {
    $gameData = [
        'id' => '123',
        'name' => 'Factorio',
        'box_art_url' => fix_box_art('https://static-cdn.jtvnw.net/ttv-boxart/130942_IGDB-52x72.jpg')
    ];

    expect(Game::count())->toBe(0);
    expect(Suggestion::count())->toBe(0);
    expect(Vote::count())->toBe(0);

    Livewire::actingAs($this->user)
        ->test(Create::class, ['feed' => $this->feed])
        ->dispatch('game-searched', $gameData)
        ->set('form.gameMode', 'multiplayer')
        ->call('store')
        ->assertOk()
        ->assertDispatched('game-update')
        ->assertSet('form.gameMode', null);

    expect(Game::count())->toBe(1);

    expect(Game::first())
        ->name->toBe('Factorio')
        ->game_id->toBe('123');

    expect(Suggestion::count())->toBe(1);

    expect(Suggestion::first())
        ->feed_id->toBe($this->feed->getKey())
        ->user_id->toBe($this->user->getKey())
        ->game_mode->toBe('multiplayer');

    expect(Vote::count())->toBe(1);

    expect(Vote::first())
        ->group_id->toBe($this->group->getKey())
        ->user_id->toBe($this->user->getKey())
        ->vote->toBe(Vote::UP_VOTE);
});

test('suggestion creator can update their suggestion if it is not custom', function() {
    $suggestion = Suggestion::factory()
        ->forFeed($this->feed)
        ->forUser($this->user)
        ->create(['game_mode' => 'singleplayer']);

    $newGameMode = 'multiplayer';

    Livewire::actingAs($this->user)
        ->test(Edit::class, ['suggestion' => $suggestion])
        ->call('edit')
        ->set('form.gameMode', $newGameMode)
        ->call('update')
        ->assertOk()
        ->assertDispatched('game-update')
        ->assertSet('form.gameMode', null);

    expect(Suggestion::count())->toBe(1);

    expect(Suggestion::first())
        ->feed_id->toBe($this->feed->getKey())
        ->user_id->toBe($this->user->getKey())
        ->game_mode->toBe($newGameMode);
});

test('suggestion can not be updated by someone other than its creator', function() {
    $suggestion = Suggestion::factory()
        ->forFeed($this->feed)
        ->forUser($this->user)
        ->create(['game_mode' => 'singleplayer']);

    Livewire::actingAs(User::factory()->create())
        ->test(Edit::class, ['suggestion' => $suggestion])
        ->call('edit')
        ->set('form.gameMode', 'multiplayer')
        ->call('update')
        ->assertForbidden();

    expect(Suggestion::first())
        ->game_mode->toBe('singleplayer')
        ->user_id->toBe($this->user->getKey());
});

test('suggestion creator can delete their suggestion', function() {
    $suggestion = Suggestion::factory()
        ->forFeed($this->feed)
        ->forUser($this->user)
        ->create(['game_mode' => 'singleplayer']);

    Livewire::actingAs($this->user)
        ->test(Edit::class, ['suggestion' => $suggestion])
        ->call('destroy')
        ->assertOk()
        ->assertDispatched('game-update');

    expect(Suggestion::count())->toBe(0);
    expect(Vote::count())->toBe(0);
});

test('suggestion can not be deleted by someone other than its creator', function() {
    $suggestion = Suggestion::factory()
        ->forFeed($this->feed)
        ->forUser($this->user)
        ->create(['game_mode' => 'singleplayer']);

    Livewire::actingAs(User::factory()->create())
        ->test(Edit::class, ['suggestion' => $suggestion])
        ->call('destroy')
        ->assertForbidden();

    expect(Suggestion::count())->toBe(1);
    expect(Vote::count())->toBe(1);
});

test('a suggestion with a custom game cannot be updated by anyone', function() {
    $suggestion = Suggestion::factory()
        ->forFeed($this->feed)
        ->forUser($this->user)
        ->create();

    $suggestion->game()->update(['is_custom' => true]);

    $suggestion = $suggestion->refresh();

    Livewire::actingAs($this->user)
        ->test(Edit::class, ['suggestion' => $suggestion])
        ->assertOk()
        ->call('edit')
        ->assertForbidden();
});
