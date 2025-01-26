<?php

namespace App\Actions\Suggestions;

use App\Models\Suggestion;
use Illuminate\Support\Facades\DB;

final class UpdateSuggestion
{
    public function handle(Suggestion $suggestion, array $attributes)
    {
        DB::transaction(function() use ($suggestion, $attributes) {
            $suggestion->update(['game_mode' => $attributes['game_mode'] == '' ? null : $attributes['game_mode']]);
            
            if ($suggestion->game->is_custom) {
                $suggestion->game()->update([
                    'name' => $attributes['game_name']
                ]);
            }
        });
    }
}