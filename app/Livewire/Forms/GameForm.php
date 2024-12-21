<?php

namespace App\Livewire\Forms;

use App\Models\Game;
use App\Models\Suggestion;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class GameForm extends Form
{
    #[Validate('max:24')]
    public string $gameMode = '';

    public function store($list, $searchedGame)
    {
        $this->validate();

        $game = Game::updateOrCreate([
            'game_id' => $searchedGame['id']
        ], [
            'name' => $searchedGame['name'],
            'cover' => $searchedGame['box_art_url'],
        ]);

        Suggestion::create([
            'list_id' => $list->getKey(),
            'game_id' => $game->getKey(),
            'user_id' => auth()->id(),
            'game_mode' => $this->gameMode,
        ]);
    }

    public function update($suggestion)
    {
        $this->validate();

        $suggestion->update([
            'game_mode' => $this->gameMode,
        ]);

        Flux::modal("edit-game-{$suggestion->game->getKey()}")->close();
        Flux::toast(variant: 'success', text: 'Updated Successfully!', duration: 3000);
    }

    public function destroy($suggestion)
    {
        $suggestion->votes()->forceDelete();
        $suggestion->forceDelete();

        Flux::toast(variant: 'success', text: 'Deleted Successfully!', duration: 3000);
    }
}