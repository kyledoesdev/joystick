<?php

namespace App\Livewire\Suggestions;

use App\Models\Feed;
use App\Models\Vote;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Feed $feed;
    public string $search = '';

    public function render()
    {
        return view('livewire.feed.show');
    }

    #[Computed]
    #[On('game-update')]
    public function suggestions()
    {
        return $this->feed->suggestions()
            ->when($this->search != '', function($query) {
                $query->whereHas('game', fn($q2) => $q2->where('name', 'like', "%{$this->search}%"));
            })
            ->withCount([
                'votes as positive_votes_count' => fn($q) => $q->where('vote', Vote::UP_VOTE),
                'votes as neutral_votes_count' => fn($q) => $q->where('vote', Vote::NEUTRAL),
                'votes as down_votes_count' => fn($q) => $q->where('vote', Vote::DOWN_VOTE),
                'votes as all_votes_count'
            ])
            ->with('user', 'game', 'votes')
            ->orderBy('positive_votes_count', 'desc')
            ->orderBy('neutral_votes_count', 'desc')
            ->orderBy('down_votes_count', 'desc')
            ->get();
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
            'group_id' => $this->feed->group->getKey(),
            'suggestion_id' => $suggestionId,
            'user_id' => auth()->id(),
        ], [
            'vote' => $vote,
        ]);

        Flux::toast(variant: 'success', text: "Vote Saved!", duration: 3000);
    }
}
