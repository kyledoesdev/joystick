<?php

namespace App\Actions\Suggestions;

use App\Models\Suggestion;
use Illuminate\Support\Facades\DB;

final class DestroySuggestion
{
    public function handle(Suggestion $suggestion)
    {
        DB::transaction(function() use ($suggestion) {
            /* delete the game if it is custom */
            if ($suggestion->game->is_custom == true) {
                $suggestion->game()->delete();
            }

            /* delete the votes for the suggestion */
            $suggestion->votes()->delete();

            /* delete the suggestion - force deleting for now - cant see a scenario where reviving one would be necessary*/
            $suggestion->forceDelete();
        });
    }
}