<?php

namespace App\Livewire\Forms;

use App\Models\Feed;
use App\Models\Group;
use App\Models\Invite;
use App\Models\InviteStatus;
use Flux\Flux;
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

    #[Validate('required')]
    public array $invited_users = [];

    public ?Group $group = null;

    public function store()
    {
        $this->validate();

        $group = Group::create([
            'name' => $this->name,
            'owner_id' => auth()->id(),
            'owner_feeds_only' => $this->ownerFeedsOnly
        ]);

        Feed::create([
            'group_id' => $group->getKey(),
            'user_id' => auth()->id(),
            'name' => Str::possessive($group->name) . ' Game Backlog'
        ]);

        foreach(collect($this->invited_users) as $user) {
            Invite::create([
                'group_id' => $group->getKey(),
                'user_id' => $user,
                'invited_at' => now(),
                'status_id' => auth()->id() == $user 
                    ? InviteStatus::ACCEPTED 
                    : InviteStatus::PENDING
            ]);
        }
    }

    public function edit($group)
    {
        $this->group = $group;

        $this->name = $this->group->name;
        $this->invited_users = $this->group->invites->whereNotIn('status_id', [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT])->pluck('user_id')->toArray();
        $this->ownerFeedsOnly = $this->group->owner_feeds_only;
    }

    public function update($group)
    {
        $this->validate();

        $this->group->update([
            'name' => $this->name,
            'owner_feeds_only' => $this->ownerFeedsOnly
        ]);
        
        $groupInvites = $this->group->invites()
            ->where('status_id', '!=', InviteStatus::DECLINED)
            ->with('group')
            ->get();
        
        $this->handleNewUsers($groupInvites);
        $this->handleUninvitedUsers($groupInvites);
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

    private function handleNewUsers($groupInvites): void
    {
        collect($this->invited_users)->each(function ($userId) use ($groupInvites) {
            $userInvite = $groupInvites->firstWhere('user_id', $userId);

            if (is_null($userInvite)) {
                $this->group->invites()->create([
                    'user_id' => $userId,
                    'status_id' => InviteStatus::PENDING,
                    'invited_at' => now(),
                ]);

                //send notification
            } elseif ($userInvite->status_id === InviteStatus::OWNER_REMOVED) {
                $userInvite->update([
                    'status_id' => InviteStatus::PENDING,
                ]);
            }
        });
    }

    private function handleUninvitedUsers($groupInvites): void
    {
        $groupInvites->each(function ($invite) {
            if (! collect($this->invited_users)->contains($invite->user_id)) {
                $invite->update(['status_id' => InviteStatus::OWNER_REMOVED]);
            }
        });
    }
}
