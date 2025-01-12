<?php

use App\Livewire\Feed\Show;
use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function() {
    $this->user = User::factory()->create();
    $this->group = Group::factory()->withOwner($this->user)->ownerFeedsOnly(true)->create();

    $this->member = User::factory()->create();
    $this->group->invites()->create([
        'user_id' => $this->member->getKey(),
        'status_id' => InviteStatus::ACCEPTED,
        'invited_at' => now(),
        'responded_at' => now()
    ]);
});

test('can load feeds page for a group owner', function () {
    $this->actingAs($this->user)->get(route('group', ['group' => $this->group]))->assertOk();
});

test('can load feeds page for a group member who has accepted their invitation', function () {
    $this->actingAs($this->member)->get(route('group', ['group' => $this->group]))->assertOk();
});

test('can not load feeds page for a user not invited to the group', function() {
    $notInvitedUser = User::factory()->create();

    $this->actingAs($notInvitedUser)->get(route('group', ['group' => $this->group]))->assertForbidden();
});

test('group owner can create a new feed', function() {
    Livewire::actingAs($this->user)
        ->test(Show::class, ['group' => $this->group])
        ->set('createForm.name', 'Foo')
        ->call('store')
        ->assertOk();

    $this->assertDatabaseHas('feeds', [
        'group_id' => $this->group->getKey(),
        'name' => 'Foo',
    ]);
});

test('group member can create a new feed if the group owner allows it', function() {
    $this->group->update(['owner_feeds_only' => false]);

    Livewire::actingAs($this->member)
        ->test(Show::class, ['group' => $this->group])
        ->set('createForm.name', 'Foo')
        ->call('store')
        ->assertOk();

    $this->assertDatabaseHas('feeds', [
        'group_id' => $this->group->getKey(),
        'name' => 'Foo',
    ]);
});

test('group member can not create a new feed if the group owner does not allow it', function() {
    Livewire::actingAs($this->member)
        ->test(Show::class, ['group' => $this->group])
        ->set('createForm.name', 'Foo')
        ->call('store')
        ->assertForbidden();
});

test('feed owner can edit their feed', function() {
    Livewire::actingAs($this->user)
        ->test(Show::class, ['group' => $this->group])
        ->call('edit', $this->group->feeds->first()->getKey())
        ->assertSet('editForm.name', $this->group->feeds->first()->name)
        ->set('editForm.name', 'Foo')
        ->call('update')
        ->assertOk();

    $this->assertDatabaseHas('feeds', [
        'group_id' => $this->group->getKey(),
        'name' => 'Foo',
    ]);
});

test('feed can not be edited by non owner', function() {
    Livewire::actingAs($this->member)
        ->test(Show::class, ['group' => $this->group])
        ->call('edit', $this->group->feeds->first()->getKey())
        ->assertForbidden();
});

test('user can delete their own feed', function() {
    Livewire::actingAs($this->user)
        ->test(Show::class, ['group' => $this->group])
        ->call('confirm', $this->group->feeds->first()->getKey())
        ->assertSet('editForm.feed', $this->group->feeds->first())
        ->call('destroy')
        ->assertOk();

    $this->assertDatabaseMissing('feeds', [
        'group_id' => $this->group->getKey(),
        'name' => 'Foo',
    ]);
});

test('user can not delete a feed they do not own', function() {
    Livewire::actingAs($this->member)
        ->test(Show::class, ['group' => $this->group])
        ->call('confirm', $this->group->feeds->first()->getKey())
        ->assertForbidden();
});
