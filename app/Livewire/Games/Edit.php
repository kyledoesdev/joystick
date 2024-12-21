<?php

namespace App\Livewire\Games;

use App\Livewire\Forms\GameForm;
use App\Models\Suggestion;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public GameForm $form;
    public Suggestion $suggestion;

    public function render()
    {
        return view('livewire.games.edit');
    }

    public function edit()
    {
        $this->form->gameMode = $this->suggestion->game_mode;
    }

    public function update()
    {
        $this->form->update($this->suggestion);
    }

    public function destroy()
    {
        $this->form->destroy($this->suggestion);

        $this->dispatch('game-deleted');
    }
}
