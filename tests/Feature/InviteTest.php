<?php

use App\Livewire\Dashboard;
use App\Livewire\Invites\Index;
use App\Livewire\Invites\NavigationBadge;
use App\Models\Feed;
use App\Models\Group;
use App\Models\Invite;
use App\Models\InviteStatus;
use App\Models\Suggestion;
use App\Models\User;
use App\Models\UserGroupPreference;
use App\Models\Vote;
use Livewire\Livewire;

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->group = Group::factory()->create();

    /* $this->user is not the owner of $this->group for these tests */
    $this->invite = Invite::factory()
        ->forGroup($this->group)
        ->forUser($this->user)
        ->withStatus(InviteStatus::PENDING)
        ->create();
});

test('can load invites page', function() {
    $this->actingAs($this->user)->get(route('invites'))->assertOk();
});

test('can see pending invites', function() {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertOk()
        ->assertSee($this->group->name)
        ->assertSee($this->group->owner->name);

    Livewire::actingAs($this->user)
        ->test(NavigationBadge::class)
        ->assertSee('1');
});

test('can not see invites that are not pending', function() {
    $this->invite->update(['status_id' => InviteStatus::ACCEPTED]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertDontSee($this->group->name)
        ->assertDontSee($this->group->owner->name)
        ->assertSee('No invites left to review.');

    Livewire::actingAs($this->user)
        ->test(NavigationBadge::class)
        ->assertSee('Invitations');
});

test('can not see another user\'s pending invites', function() {
    $anotherInvite = Invite::factory()->withStatus(InviteStatus::PENDING)->create();

    $this->invite->update(['status_id' => InviteStatus::ACCEPTED]);

    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->assertSee('No invites left to review.');
});

test('user can accept an invite', function() {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('update', $this->invite->getKey(), InviteStatus::ACCEPTED)
        ->assertOk()
        ->assertDispatched('invitation-updated');

    $this->invite->refresh();

    expect($this->invite->status_id)->toBe(InviteStatus::ACCEPTED);
    expect(UserGroupPreference::count())->toBe(2); /* 1 for the owner of the group, and one for this invited user */
    expect(UserGroupPreference::where('user_id', $this->user->getKey())->exists())->toBeTrue();
});

test('user can decline an invite', function() {
    Livewire::actingAs($this->user)
        ->test(Index::class)
        ->call('update', $this->invite->getKey(), InviteStatus::DECLINED)
        ->assertOk()
        ->assertDispatched('invitation-updated');

    $this->invite->refresh();

    expect($this->invite->status_id)->toBe(InviteStatus::DECLINED);
});

test('user can leave a group', function() {
    $this->invite->update(['status_id' => InviteStatus::ACCEPTED]);

    $this->invite->group->userPreferences()->create([
        'user_id' => $this->user->getKey()
    ]);

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertOk()
        ->call('confirmLeaveGroup', $this->group->getKey())
        ->assertSet('inviteForm.invite.id', $this->invite->getKey())
        ->assertOk()
        ->call('leaveGroup', $this->invite->getKey())
        ->assertOk();
});

test('user can not leave a group they are not in', function() {
    $this->invite->update(['status_id' => InviteStatus::ACCEPTED]);

    $this->invite->group->userPreferences()->create([
        'user_id' => $this->user->getKey()
    ]);

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertOk()
        ->call('confirmLeaveGroup', Group::factory()->create()->getKey())
        ->assertForbidden();
});
