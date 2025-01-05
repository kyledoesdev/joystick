<div>
    <x-slot name="header">{{ $feed->name }}</x-slot>
    <x-slot name="subheader">{{ $feed->start_time }}</x-slot>
    <x-slot name="action">
        <flux:button size="sm" href="{{ route('group', ['groupId' => $feed->group->getKey()]) }}" variant="primary">
            Back
        </flux:button>
    </x-slot>

    <flux:card class="my-4">
        <div class="flex justify-between">
            <livewire:suggestions.search />
            <livewire:suggestions.create :feed="$feed" />
        </div>
    </flux:card>

    <div class="flex justify-center">
        <div class="lg:w-1/3">
            @forelse ($this->suggestions as $suggestion)               
                <flux:card
                    class="my-4"
                    wire:key="game-suggestion-{{ $suggestion->getKey() }}"
                >
                    <x-suggestions.header :suggestion="$suggestion" />
                    <x-suggestions.body :suggestion="$suggestion" />
                    <x-suggestions.footer :suggestion="$suggestion" />
                </flux:card>
            @empty
                <x-empty-collection :search="$search" message="No games found in this feed." />
            @endforelse
        </div>
    </div>
</div>