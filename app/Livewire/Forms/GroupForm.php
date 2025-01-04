<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Group\UnInviteUsers;
use App\Livewire\Actions\Group\UpdateInvitedUsers;
use App\Models\Feed;
use App\Models\Group;
use App\Models\Invite;
use App\Models\InviteStatus;
use Exception;
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
    public $discordWebHook = '';

    #[Validate('nullable|boolean')]
    public $discordUpdates = false;

    public array $invited_users = [];

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

        $this->invited_users = $this->group->invites
            ->whereNotIn('status_id', [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT])
            ->pluck('user_id')
            ->toArray();
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
        
        $groupInvites = $this->group->invites()
            ->where('status_id', '!=', InviteStatus::DECLINED)
            ->get();
        
        (new UpdateInvitedUsers($this->group, collect($this->invited_users), $groupInvites))->handle();
        (new UnInviteUsers($this->group, collect($this->invited_users), $groupInvites))->handle();

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
