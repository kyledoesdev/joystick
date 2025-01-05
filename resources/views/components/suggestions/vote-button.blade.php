@props([
    'suggestion',
    'type',
    'voters'
])

@php
    use App\Models\Vote;

    $message = 'No voters 🤷‍♀️';

    if (count($voters)) {
        if (count($voters) > 5) {
            $message = $voters->join(', ') . ' & ' . ($suggestion->all_votes_count - 5) . ' others';
        } else {
            $message = $voters->join(', ');
        }
    }
@endphp

<flux:tooltip :content="$message">
    <flux:button wire:click="store({{ $suggestion->getKey() }}, {{ $type }})">
        <div class="flex">
            <div>
                @if ($type == Vote::UP_VOTE)
                    <flux:icon.arrow-up-circle />
                @elseif ($type == Vote::DOWN_VOTE)
                    <flux:icon.arrow-down-circle />
                @else
                    <flux:icon.minus-circle />
                @endif
            </div>
            <div class="ml-2">
                @if ($type == Vote::UP_VOTE)
                    <flux:badge size="sm" color="lime">
                        {{ $suggestion->positive_votes_count }}
                    </flux:badge>
                @elseif ($type == Vote::DOWN_VOTE)
                    <flux:badge size="sm" color="red">
                        {{ $suggestion->down_votes_count }}
                    </flux:badge>
                @else
                    <flux:badge size="sm" color="yellow">
                        {{ $suggestion->neutral_votes_count }}
                    </flux:badge>
                @endif
            </div>
        </div>
    </flux:button>
</flux:tooltip>