<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\FeedForm;
use App\Models\Feed;
use App\Models\Group;
use App\Models\InviteStatus;
use App\Models\Vote;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ShowGroup extends Component
{
    use WithPagination;

    public Group $group;

    public FeedForm $createForm;
    public FeedForm $editForm;

    public function render()
    {
        return view('livewire.groups.show');
    }

    #[Computed]
    public function feeds()
    {
        return $this->group->feeds()
            ->withCount('suggestions', 'votes')
            ->orderBy('start_time', 'desc')
            ->get();
    }

    #[Computed]
    public function members()
    {
        return $this->group->invites()
            ->select(
                'invites.user_id',
                'invites.status_id',
                'users.name as user_name',
                'users.avatar as avatar',
                DB::raw("(SELECT COUNT(*) FROM votes WHERE votes.user_id = invites.user_id AND votes.group_id = {$this->group->getKey()}) as vote_count")
            )
            ->join('users', 'users.id', '=', 'invites.user_id')
            ->where('invites.status_id', InviteStatus::ACCEPTED)
            ->orderBy('vote_count', 'desc')
            ->orderBy('users.name', 'asc')
            ->paginate(6);
    }

    public function getHighestVoteCountProperty()
    {
        return Vote::query()
            ->selectRaw('count(*) as top_vote_count')
            ->where('group_id', $this->group->getKey())
            ->groupBy('user_id')
            ->orderBy('top_vote_count', 'desc')
            ->value('top_vote_count') ?? 0;
    }

    public function store()
    {
        $this->createForm->store($this->group);
    }

    public function edit($feedId)
    {
        $this->editForm->edit($feedId);
    }

    public function update()
    {
        $this->editForm->update();
    }

    public function confirm($feedId)
    {
        $this->editForm->confirm($feedId);
    }

    public function destroy()
    {
        $this->editForm->destroy();
    }
}
