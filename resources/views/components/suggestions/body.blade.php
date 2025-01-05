@php
    use App\Models\Vote;

    $upVoters = $suggestion->votes->where('vote', Vote::UP_VOTE)->take(5)->pluck('user.name');
    $downVoters = $suggestion->votes->where('vote', Vote::DOWN_VOTE)->take(5)->pluck('user.name');
    $neutralVoters = $suggestion->votes->where('vote', Vote::NEUTRAL)->take(5)->pluck('user.name');
@endphp

<div class="flex justify-between mb-4">
    <div class="mt-2">
        <x-game-cover :game="$suggestion->game"></x-game-cover>
    </div>

    {{-- Votes todo abstract --}}
    <div class="flex flex-col justify-center">
        <div class="my-2">
            <x-suggestions.vote-button
                :suggestion="$suggestion"
                :type="Vote::UP_VOTE"
                :voters="$upVoters"
            />
        </div>
        <div class="my-2">
            <x-suggestions.vote-button
                :suggestion="$suggestion"
                :type="Vote::NEUTRAL"
                :voters="$neutralVoters"
            />
        </div>
        <div class="my-2">
            <x-suggestions.vote-button
                :suggestion="$suggestion"
                :type="Vote::DOWN_VOTE"
                :voters="$downVoters"
            />
        </div>
    </div>
</div>

<flux:separator class="mt-1" />