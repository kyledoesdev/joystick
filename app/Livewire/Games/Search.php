<?php

namespace App\Livewire\Games;

use App\Http\Api\Twitch;
use App\Models\Game;
use App\Models\Group;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Search extends Component
{
    public Group $group;
    public string $phrase = '';

    public function render()
    {
        return view('livewire.games.search');
    }

    public function search()
    {
        $response = (new Twitch(auth()->user()))->search($this->phrase);

        if ($response->successful()) {
            $searchedGame = $response->json('data')[0] ?? null;

            if (is_null($searchedGame)) {
                Flux::toast(variant: 'warning', text: "No games found for: $this->phrase.", duration: 2000);
                $this->phrase = '';
                return;
            }

            $searchedGame['box_art_url'] = fix_box_art($searchedGame['box_art_url']);

            $this->dispatch('game-searched', $searchedGame);
        } else {
            Flux::toast(variant: 'danger', text: "Could not connect to twitch to search for your game. Please try again later.", duration: 0);
        }
    }

    #[On('game-search-cleared')]
    public function clear()
    {
        $this->phrase = '';
    }
}
