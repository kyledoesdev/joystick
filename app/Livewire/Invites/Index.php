<?php

namespace App\Livewire\Invites;

use App\Livewire\Forms\InviteForm;
use App\Livewire\Traits\TableHelpers;
use App\Models\Invite;
use App\Models\InviteStatus;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use TableHelpers;
    use WithPagination;

    public InviteForm $form;

    public function mount()
    {
        $this->sortBy = 'invited_at';
    }

    public function render()
    {
        return view('livewire.invites.index');
    }

    #[Computed]
    public function invites()
    {
        return Invite::query()
            ->select([
                'groups.id as group_id',
                'groups.name as group_name',
                'owners.avatar as owner_avatar',
                'owners.name as owner_name',
                'invites.id as invite_id',
                'invites.invited_at',
                'invites.status_id',
            ])
            ->join('groups', 'invites.group_id', '=', 'groups.id')
            ->join('users as owners', 'groups.owner_id', '=', 'owners.id')
            ->join('users', function ($join) {
                $join->on('users.id', '=', 'invites.user_id')
                    ->where('invites.user_id', '=', auth()->id());
            })
            ->where('invites.status_id', InviteStatus::PENDING)
            ->tap(fn ($query) => $this->sortBy ? $query->orderBy($this->sortBy, $this->sortDirection) : $query)
            ->get();
    }

    public function update($inviteId, $status)
    {
        $this->form->update($inviteId, $status);

        $this->dispatch('invitation-updated');
    }
}
