<?php

use App\Livewire\Dashboard;
use App\Livewire\Group\Edit;
use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function() {
    $this->user = User::factory()->create();
});

test('can load the dashboard', function() {
    $this->actingAs($this->user)->get(route('dashboard'))->assertOk();
});

test('user can see their groups', function() {
    $group = Group::factory()->withOwner($this->user)->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    $this->actingAs($this->user)->get(route('dashboard'))->assertOk();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertSee($group->name);
});

test('user can\'t see groups they aren\'t a part of', function() {
    $invitedGroup = Group::factory()->withOwner($this->user)->create();
    $nonInvitedGroup = Group::factory()->create();

    $this->assertDatabaseHas('groups', [
        'id' => $invitedGroup->getKey(),
        'id' => $nonInvitedGroup->getKey()
    ]);

    $this->actingAs($this->user)->get(route('dashboard'))->assertOk();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertSee($invitedGroup->name)
        ->assertDontSee($nonInvitedGroup->name);
});

test('user can create a new group', function() {
    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->set('form.name', 'Foo')
        ->call('store')
        ->assertSee('Foo');

    $this->assertDatabaseHas('groups', [
        'name' => 'Foo',
    ]);

    $group = Group::where('name', 'Foo')->where('owner_id', $this->user->getKey())->firstOrFail();

    $this->assertDatabaseHas('invites', [
        'group_id' => $group->getKey(),
        'user_id' => $this->user->getKey(),
        'status_id' => InviteStatus::ACCEPTED,
    ]);

    $this->assertDatabaseHas('feeds', [
        'group_id' => $group->getKey(),
        'user_id' => $this->user->getKey()
    ]);
});

test('user can edit group they own', function() {
    $group = Group::factory()->withOwner($this->user)->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    $this->actingAs($this->user)->get(route('group.edit', ['id' => $group->getKey()]))->assertOk();
});

test('user can not edit a group they do not own', function() {
    $group = Group::factory()->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    $this->actingAs($this->user)->get(route('group.edit', ['id' => $group->getKey()]))->assertForbidden();
});

test('user can delete a group they own', function() {
    $group = Group::factory()->withOwner($this->user)->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('confirm', $group->getKey())
        ->assertSet('form.group.id', $group->getKey())
        ->assertSee("Delete Group: {$group->name}")
        ->call('destroy');

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
        'deleted_at' => now(),
    ]);
});

test('user can not delete a group they do not own', function() {
    $group = Group::factory()->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('confirm', $group->getKey())
        ->assertSet('form.group.id', $group->getKey())
        ->assertSee("Delete Group: {$group->name}")
        ->call('destroy')
        ->assertForbidden();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
        'deleted_at' => null,
    ]);
});