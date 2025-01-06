<?php

namespace App\Livewire\Forms;

use App\Models\Feed;
use App\Models\Group;
use App\Models\Invite;
use App\Models\InviteStatus;
use Flux\Flux;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithPagination;

class GroupForm extends Form
{
    #[Validate('required|string|min:3|max:36')]
    public string $name = '';

    #[Validate('required|boolean')]
    public bool $ownerFeedsOnly = false;

    #[Validate('nullable|string|max:255')]
    public $discordWebHook = null;

    #[Validate('nullable|boolean')]
    public $discordUpdates = false;

    public ?Group $group = null;

    public function store()
    {
        $this->validate();

        $group = Group::create([
            'name' => $this->name,
            'owner_id' => auth()->id(),
            'discord_webhook_url' => $this->discordWebHook,
            'discord_updates' => $this->discordUpdates,
            'owner_feeds_only' => $this->ownerFeedsOnly
        ]);

        /* Auto create a game backlog feed */
        $group->feeds()->create([
            'user_id' => auth()->id(),
            'name' => Str::possessive($group->name) . ' Game Backlog'
        ]);

        /* auto create the accepted invite for the group owner */
        $group->invites()->create([
            'user_id' => auth()->id(),
            'invited_at' => now(),
            'responded_at' => now(),
            'status_id' => InviteStatus::ACCEPTED 
        ]);

        $this->reset();

        Flux::modal('create-group')->close();
        Flux::toast(variant: 'success', text: 'Group Created - Go Invite your friends!', duration: 3000);
    }

    public function edit($group)
    {
        $this->group = $group;
        $this->name = $this->group->name;
        $this->ownerFeedsOnly = $this->group->owner_feeds_only;
        $this->discordWebHook = $this->group->discord_webhook_url;
        $this->discordUpdates = $this->group->discord_updates;
    }

    public function update($group)
    {
        $this->validate();

        $this->group->update([
            'name' => $this->name,
            'owner_feeds_only' => $this->ownerFeedsOnly,
            'discord_webhook_url' => $this->discordWebHook,
            'discord_updates' => $this->discordUpdates,
        ]);
        
        Flux::toast(variant: 'success', text: 'Group Updated!', duration: 3000);
    }

    public function confirm($groupId)
    {
        $this->group = Group::findOrFail($groupId);

        Flux::modal('destroy-group')->show();
    }

    public function destroy()
    {
        $this->group->delete();

        Flux::modal('destroy-group')->close();
        Flux::toast(variant: 'success', text: 'Group Deleted!', duration: 3000);
    }
}
