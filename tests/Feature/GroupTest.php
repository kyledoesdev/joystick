<?php

use App\Livewire\Dashboard;
use App\Livewire\Group\Edit;
use App\Models\Feed;
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

test('user can not see groups they are not a part of', function() {
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
});

test('creating a group creates a backlog feed', function() {
    $group = Group::factory()->withOwner($this->user)->create();

    expect(count($group->feeds))->toBe(1);
    $this->assertStringContainsString('Backlog', $group->feeds->first()->name);
});

test('creating a group accepts invite for owner', function() {
    $group = Group::factory()->withOwner($this->user)->create();
    
    expect(count($group->invites->where('user_id', $this->user->getKey())->where('status_id', InviteStatus::ACCEPTED)))->toBe(1);
});

test('user can edit group they own', function() {
    $group = Group::factory()->withOwner($this->user)->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    $this->actingAs($this->user)->get(route('group.edit', $group))->assertOk();
});

test('user can not edit a group they do not own', function() {
    $group = Group::factory()->create();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
    ]);

    $this->actingAs($this->user)->get(route('group.edit', $group))->assertForbidden();
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