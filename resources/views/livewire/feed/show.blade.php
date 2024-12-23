<div>
    <x-slot name="header">{{ $feed->name }}</x-slot>
    <x-slot name="subheader">{{ $feed->start_time }}</x-slot>
    <x-slot name="action">
        <flux:button size="sm" href="{{ url()->previous() }}" variant="primary">
            Back
        </flux:button>
    </x-slot>

    <flux:card class="my-4">
        <div class="flex justify-between">
            <livewire:feed.search />
            <livewire:games.create :feed="$feed" />
        </div>
    </flux:card>

    <div class="flex justify-center">
        <div class="lg:w-1/3">
            @forelse ($suggestions as $suggestion)               
                <flux:card
                    class="my-4"
                    wire:key="game-suggestion-{{ $suggestion->getKey() }}"
                >
                    <div class="flex justify-between">
                        <span>Suggested by: {{ $suggestion->user->name }}</span>
                        <livewire:feed.voters-table
                            :suggestion="$suggestion"
                            wire:key="vote-table-{{ $suggestion->getKey() }}"
                        />
                    </div>
    
                    <flux:separator class="mt-1" />
    
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

                    @if ($suggestion->user_id == auth()->id())
                        <flux:separator class="mt-1" />

                        <div class="flex justify-end mt-2 gap-x-1">
                            <livewire:games.edit
                                :suggestion="$suggestion"
                                wire:key="edit-fields-{{ $suggestion->getKey() }}"
                            />
                        </div>
                    @endif
                </flux:card>
            @empty
                <x-empty-table :search="$search" message="No games found in this feed." />
            @endforelse
        </div>
    </div>
</div>