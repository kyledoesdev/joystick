<?php

namespace App\Livewire\Forms;

use App\Actions\Suggestions\UpdateSuggestion;
use App\Actions\Suggestions\DestroySuggestion;
use App\Actions\Suggestions\StoreSuggestion;
use App\Models\Game;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Form;

class SuggestionForm extends Form
{
    #[Validate('max:24')]
    public ?string $gameMode = null;

    #[Validate('max:35')]
    public ?string $customGameName = null;

    public bool $customGame = false;
    
    public function store($feed, $searchedGame)
    {
        $this->validate();

        if (is_null($searchedGame)) {
            $searchedGame = [
                'id' => Str::uuid(),
                'name' => $this->customGameName,
                'box_art_url' => Game::getBlankCover(),
                'is_custom' => true,
            ];
        }

        (new StoreSuggestion)->handle(auth()->user(), [
            'feed' => $feed,
            'game_mode' =>$this->gameMode,
            'game' => $searchedGame
        ]);

        $this->reset();
    }

    public function update($suggestion)
    {
        $this->validate();

        abort_if($suggestion->user_id !== auth()->id(), 403);

        (new UpdateSuggestion)->handle($suggestion, [
            'game_name' => $this->customGameName,
            'game_mode' => $this->gameMode
        ]);
        
        $this->reset();

        Flux::modal("edit-game-{$suggestion->getKey()}")->close();
        Flux::toast(variant: 'success', text: 'Updated Successfully!', duration: 3000);
    }

    public function destroy($suggestion)
    {
        abort_if($suggestion->user_id !== auth()->id(), 403);

        (new DestroySuggestion)->handle($suggestion);

        Flux::toast(variant: 'success', text: 'Deleted Successfully!', duration: 3000);
    }
}