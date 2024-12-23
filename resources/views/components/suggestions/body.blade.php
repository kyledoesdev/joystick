<div class="flex justify-between mb-4">
    <div class="mt-2">
        <x-game-cover :game="$suggestion->game"></x-game-cover>
    </div>

    {{-- Votes todo abstract --}}
    <div class="flex flex-col justify-center">
        <div class="my-2">
            <flux:button wire:click="store({{ $suggestion->getKey() }}, {{ App\Models\Vote::UP_VOTE }})">
                <div class="flex">
                    <div>
                        <flux:icon.arrow-up-circle />
                    </div>
                    <div class="ml-2">
                        <flux:badge size="sm" color="lime">
                            {{ $suggestion->positive_votes_count }}
                        </flux:badge>
                    </div>
                </div>
            </flux:button>
        </div>
        <div class="my-2">
            <flux:button wire:click="store({{ $suggestion->getKey() }}, {{ App\Models\Vote::NEUTRAL }})">
                <div class="flex">
                    <div>
                        <flux:icon.minus-circle />
                    </div>
                    <div class="ml-2">
                        <flux:badge size="sm">
                            {{ $suggestion->neutral_votes_count }}
                        </flux:badge>
                    </div>
                </div>
            </flux:button>
        </div>
        <div class="my-2">
            <flux:button wire:click="store({{ $suggestion->getKey() }}, {{ App\Models\Vote::DOWN_VOTE }})">
                <div class="flex">
                    <div>
                        <flux:icon.arrow-down-circle />
                    </div>
                    <div class="ml-2">
                        <flux:badge size="sm" color="red">
                            {{ $suggestion->down_votes_count }}
                        </flux:badge>
                    </div>
                </div>
            </flux:button>
        </div>
    </div>
</div>

<flux:separator class="mt-1" />