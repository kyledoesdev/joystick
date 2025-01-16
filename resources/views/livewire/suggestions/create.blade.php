<div>
    <flux:modal.trigger name="create-game">
        <flux:button variant="primary" size="sm" icon="plus">Add Game</flux:button>
    </flux:modal.trigger>

    <flux:modal name="create-game" variant="flyout" class="space-y-6">
        <div>
            <flux:heading size="lg">Add a new game</flux:heading>
        </div>

        <livewire:games.search />

        @if (!is_null($searchedGame))
            <div class="flex justify-between">
                <h5>Game found:</h5>
                <flux:button variant="primary" size="xs" icon="x-mark" wire:click="clear" />
            </div>

            <div class="my-1 mx-1">
                <img
                    width="142" 
                    height="190"
                    class="rounded-xl my-2"
                    src="{{ $searchedGame['box_art_url'] }}"
                    alt="{{ $searchedGame['name'] }}"
                />

                <flux:separator />

                <div class="mt-4">
                    <flux:input
                        class="mt-4"
                        wire:model="form.gameMode"
                        label="Game Mode"
                        description="A game type or game mode within the selected game."
                    />
                </div>

                <div class="flex mt-4">
                    <flux:button
                        variant="primary" 
                        size="xs"
                        icon-trailing="check"
                        wire:click="store({{ $searchedGame['id'] }})"
                    >
                        {{ $searchedGame['name'] }}
                    </flux:button>
                </div>
            </div>
        @endif
    </flux:modal>
</div>
