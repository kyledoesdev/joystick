<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\GroupForm;
use App\Livewire\Forms\InviteForm;
use App\Livewire\Traits\TableHelpers;
use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\User;
use Flux\Flux;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class EditGroup extends Component
{
    use TableHelpers;
    use WithPagination;

    public GroupForm $groupForm;
    public InviteForm $inviteForm;
    public Group $group;

    public ?User $searchedUser = null;
    
    public function mount(Group $group)
    {
        $this->group = $group;

        abort_if($this->group->owner_id != auth()->id(), 403);

        $this->groupForm->edit($this->group);
        $this->inviteForm->edit($this->group);
    }

    public function render()
    {
        return view('livewire.groups.edit', [
            'members' => User::getUsersForGroup($this->group->getKey(), $this->sortBy, $this->sortDirection)
        ]);
    }

    public function storeMember($userId)
    {
        $this->inviteForm->store($this->group, $userId);

        $this->search = '';
        $this->searchedUser = null;

        Flux::modal('add-member')->close();
    }

    public function removeMember($userId)
    {
        $this->inviteForm->destroy($this->group, $userId);
    }

    public function updateGroup()
    {
        $this->groupForm->update($this->group);
    }

    public function confirm($groupId)
    {
        $this->groupForm->confirm($groupId);
    }

    public function destroyGroup()
    {
        $this->groupForm->destroy();
        
        session()->flash('success', 'You have successfully deleted the group.');

        $this->redirectRoute('dashboard');
    }

    public function updatedSearch($value)
    {
        $user = User::query()
            ->where(function(Builder $query) {
                $query->newQuery()
                    ->whereDoesntHave('invites', function (Builder $q) {
                        $q->where('group_id', $this->group->getKey());
                    })
                    ->orWhereHas('invites', function (Builder $q) {
                        $q->newQuery()
                            ->where('group_id', $this->group->getKey())
                            ->whereIn('status_id', [InviteStatus::OWNER_REMOVED, InviteStatus::USER_LEFT]);
                    });
            })
            ->where('name', 'like', "%{$value}%")
            ->first();

        $this->searchedUser = $user;
    }
}
