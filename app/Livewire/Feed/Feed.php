<?php

namespace App\Livewire\Feed;

use App\Models\GroupList;
use App\Models\Vote;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Component;

class Feed extends Component
{
    public GroupList $list;
    public string $search = '';

    public function mount()
    {
        $this->list = GroupList::with('group')->findOrFail(request()->id);
    }

    #[On('game-added')]
    #[On('game-deleted')]
    public function render()
    {
        return view('livewire.feed.show', [
            'suggestions' => $this->list->suggestions()
                ->when($this->search != '', function($query) {
                    $query->whereHas('game', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"));
                })
                ->withCount([
                    'votes as positive_votes_count' => fn($q) => $q->where('vote', Vote::UP_VOTE),
                    'votes as neutral_votes_count' => fn($q) => $q->where('vote', Vote::NEUTRAL),
                    'votes as down_votes_count' => fn($q) => $q->where('vote', Vote::DOWN_VOTE),
                ])
                ->with('user', 'game')
                ->orderBy('positive_votes_count', 'desc')
                ->orderBy('neutral_votes_count', 'desc')
                ->orderBy('down_votes_count', 'desc')
                ->get()
        ]);
    }

    #[On('search-updated')]
    public function updatedSearch($value)
    {
        $this->search = $value;
    }

    public function store($suggestionId, $vote)
    {
        if (!in_array($vote, Vote::getTypes())) {
            Flux::toast(variant: 'warning', text: "Not a valid vote action.", duration: 2000);
            return;
        }

        $vote = Vote::updateOrCreate([
            'group_id' => $this->list->group->getKey(),
            'suggestion_id' => $suggestionId,
            'user_id' => auth()->id(),
        ], [
            'vote' => $vote,
        ]);

        Flux::toast(variant: 'success', text: "Vote Saved!", duration: 3000);
    }
}
