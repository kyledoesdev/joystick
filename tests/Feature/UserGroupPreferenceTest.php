<?php

use App\Livewire\Group\Preference;
use App\Models\Group;
use App\Models\Invite;
use App\Models\InviteStatus;
use App\Models\User;
use App\Models\UserGroupPreference;
use Livewire\Livewire;

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->group = Group::factory()->withOwner($this->user)->create();
    $this->userPreference = $this->group->userPreferences()->where('user_id', $this->user->getKey())->first();
});

test('user preferences are set for group', function() {
    Livewire::actingAs($this->user)
        ->test(Preference::class, ['preference' => $this->userPreference])
        ->assertSet('form.color', '#FFFFFF');
});

test('user color preference is null if it is not set', function() {
    $this->userPreference->update(['color' => null]);

    Livewire::actingAs($this->user)
        ->test(Preference::class, ['preference' => $this->userPreference])
        ->assertSet('form.color', null);
});

test('user can update color preference', function() {
    Livewire::actingAs($this->user)
        ->test(Preference::class, ['preference' => $this->userPreference])
        ->set('form.color', '#00FF00')
        ->call('update')
        ->assertOk()
        ->assertDispatched('user-preferences-updated')
        ->assertHasNoErrors();

    expect($this->userPreference->fresh()->color)->toBe('#00FF00');
});

test('user can reset color preference', function() {
    Livewire::actingAs($this->user)
        ->test(Preference::class, ['preference' => $this->userPreference])
        ->call('resetColor')
        ->assertOk()
        ->assertSet('form.color', null)
        ->assertDispatched('user-preferences-updated')
        ->assertHasNoErrors();

    expect($this->userPreference->fresh()->color)->toBeNull();
});

test('user cannot update preferences for another user', function() {
    $otherUserInvite = Invite::factory()
        ->forGroup($this->group)
        ->withStatus(InviteStatus::ACCEPTED)
        ->create();

    Livewire::actingAs($otherUserInvite->user)
        ->test(Preference::class, ['preference' => $this->userPreference])
        ->set('form.color', '#00FF00')
        ->call('update')
        ->assertForbidden();

    expect($this->userPreference->fresh()->color)->toBe('#FFFFFF');

    $otherPreference = $this->group->userPreferences()
        ->where('user_id', $otherUserInvite->user_id)
        ->first();

    expect($otherPreference->fresh()->color)->not->toBe('#00FF00');
});