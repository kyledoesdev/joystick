<?php

use App\Livewire\Dashboard;
use App\Livewire\Group\EditGroup;
use App\Models\Feed;
use App\Models\Group;
use App\Models\GroupSetting;
use App\Models\Invite;
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

    $this->actingAs($this->user)->get(route('dashboard'))->assertOk();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertSee($group->name);
});

test('user can not see groups they are not a part of', function() {
    $invitedGroup = Group::factory()->withOwner($this->user)->create();
    $nonInvitedGroup = Group::factory()->create();

    $this->actingAs($this->user)->get(route('dashboard'))->assertOk();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertSee($invitedGroup->name)
        ->assertDontSee($nonInvitedGroup->name);
});

test('user can create a new group', function() {
    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->set('groupForm.name', 'Foo')
        ->call('store')
        ->assertSee('Foo');

    $group = Group::first();

    expect(Group::count())->toBe(1);
    expect(Feed::count())->toBe(1);
    expect(Invite::count())->toBe(1);
    expect(GroupSetting::count())->toBe(1);

    expect(Group::first())->name->toBe('Foo');
    expect(Feed::first())
        ->group_id->toBe(Group::first()->getKey())
        ->user_id->toBe($this->user->getKey());
    expect(Invite::first())
        ->group_id->toBe(Group::first()->getKey())
        ->user_id->toBe($this->user->getKey())
        ->status_id->toBe(InviteStatus::ACCEPTED);
    expect(GroupSetting::first())
        ->d_create_feed_alerts->toBeTrue()
        ->d_create_suggestion_alerts->toBeTrue();
});

test('user can edit group they own', function() {
    $group = Group::factory()->withOwner($this->user)->create();

    $this->actingAs($this->user)->get(route('group.edit', $group))->assertOk();
});

test('user can not edit a group they do not own', function() {
    $group = Group::factory()->create();

    $this->actingAs($this->user)->get(route('group.edit', $group))->assertForbidden();
});

test('user can delete a group they own', function() {
    $group = Group::factory()->withOwner($this->user)->create();
    
    Livewire::actingAs($this->user)
        ->test(EditGroup::class, ['group' => $group])
        ->call('confirm', $group->getKey())
        ->call('destroyGroup')
        ->assertRedirect(route('dashboard'));

    $this->assertSoftDeleted('groups', [
        'id' => $group->getKey(),
    ]);
});

test('user can not delete a group they do not own', function() {
    $group = Group::factory()->create(); /* owner is different than $this->user */

    Livewire::actingAs($this->user)
        ->test(EditGroup::class, ['group' => $group])
        ->assertForbidden();

    $this->assertDatabaseHas('groups', [
        'id' => $group->getKey(),
        'deleted_at' => null,
    ]);
});