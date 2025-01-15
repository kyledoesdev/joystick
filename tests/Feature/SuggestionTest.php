<?php

use App\Livewire\Games\Search;
use App\Livewire\Suggestions\Create;
use App\Livewire\Suggestions\Show;
use App\Models\Group;
use App\Models\User;

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

test('create a suggestion receives a game search event', function() {
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
