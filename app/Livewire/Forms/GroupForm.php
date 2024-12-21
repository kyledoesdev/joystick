<?php

namespace App\Livewire\Forms;

use App\Livewire\Traits\TableHelpers;
use App\Models\Feed;
use App\Models\Group;
use App\Models\Invite;
use App\Models\User;
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

    #[Validate('required')]
    public array $invited_users = [];

    public ?Group $group = null;

    public function store()
    {
        $this->validate();

        $group = Group::create([
            'name' => $this->name,
            'owner_id' => auth()->id()
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
                'status' => auth()->id() == $user 
                    ? Invite::ACCEPTED 
                    : Invite::PENDING
            ]);
        }
    }

    public function edit($group)
    {
        $this->name = $group->name;
        $this->invited_users = $group->invites->where('status', '!=', Invite::REMOVED)->pluck('user_id')->toArray();
    }

    public function update($group)
    {
        $this->validate();

        $group->update(['name' => $this->name]);
        
        $invites = $group->invites()
            ->where('status', '!=', Invite::DECLINED)
            ->with('group')
            ->get();
        
        foreach ($this->invited_users as $user) {
            if ($user == auth()->id()) {
                continue;
            }

            $userInvite = $invites->firstWhere('user_id', $user);

            if (is_null($userInvite)) {
                $group->invites()->create([
                    'user_id' => $user,
                    'status' => Invite::PENDING,
                ]);
            } else {
                if ($userInvite->status == Invite::REMOVED) {
                    $userInvite->update([
                        'status' => Invite::ACCEPTED
                    ]);
                }
            }
        }

        foreach ($invites as $invite) {
            if ($invite->user_id == auth()->id()) {
                continue;
            }

            if (! collect($this->invited_users)->contains($invite->user_id)) {
                if ($invite->status == Invite::PENDING) {
                    $invite->delete();
                } else {
                    $invite->update([
                        'status' => Invite::REMOVED,
                    ]);
                }   
            }
        }
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
