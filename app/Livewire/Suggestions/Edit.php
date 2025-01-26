<?php

namespace App\Livewire\Suggestions;

use App\Livewire\Forms\SuggestionForm;
use App\Models\Suggestion;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public SuggestionForm $form;
    public Suggestion $suggestion;

    public function render()
    {
        return view('livewire.suggestions.edit');
    }

    public function edit()
    {
        $this->form->gameMode = $this->suggestion->game_mode;

        if ($this->suggestion->game->is_custom) {
            $this->form->customGameName = $this->suggestion->game->name;
        }
    }

    public function update()
    {
        $this->form->update($this->suggestion);

        $this->dispatch('game-update');
    }

    public function destroy()
    {
        $this->form->destroy($this->suggestion);

        $this->dispatch('game-update');
    }
}
