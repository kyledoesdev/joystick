<?php

namespace App\Livewire\Games;

use App\Livewire\Forms\GameForm;
use App\Models\Feed;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Feed $feed;
    public GameForm $form;

    public $searchedGame = null;

    public function render()
    {
        return view('livewire.games.create');
    }

    #[On('game-searched')]
    public function create($game)
    {
        $this->searchedGame = $game;
    }

    public function store()
    {
        $this->form->store($this->feed, $this->searchedGame);

        $this->dispatch('game-added');

        $this->clear();

        Flux::modal('create-game')->close();
        Flux::toast(variant: 'success', text: 'Game Added!', duration: 3000);
    }

    public function clear()
    {
        $this->dispatch('game-search-cleared');

        $this->searchedGame = null;
    }
}
