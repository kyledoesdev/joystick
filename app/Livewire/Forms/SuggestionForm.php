<?php

namespace App\Livewire\Forms;

use App\Livewire\Actions\Suggestions\StoreSuggestion;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SuggestionForm extends Form
{
    #[Validate('max:24')]
    public ?string $gameMode = null;

    public function store($feed, $searchedGame)
    {
        $this->validate();

        (new StoreSuggestion)->handle(auth()->user(), [
            'feed' => $feed,
            'game_mode' =>$this->gameMode,
            'game' => $searchedGame
        ]);
    }

    public function update($suggestion)
    {
        $this->validate();

        abort_if($suggestion->user_id !== auth()->id(), 403);

        $suggestion->update(['game_mode' => $this->gameMode]);

        Flux::modal("edit-game-{$suggestion->getKey()}")->close();
        Flux::toast(variant: 'success', text: 'Updated Successfully!', duration: 3000);
    }

    public function destroy($suggestion)
    {
        abort_if($suggestion->user_id !== auth()->id(), 403);

        $suggestion->votes()->forceDelete();
        $suggestion->forceDelete();

        Flux::toast(variant: 'success', text: 'Deleted Successfully!', duration: 3000);
    }
}