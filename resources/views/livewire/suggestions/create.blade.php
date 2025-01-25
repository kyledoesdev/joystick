<div>
    <flux:modal.trigger name="create-game">
        <flux:button variant="primary" size="sm" icon="plus">Create Suggestion</flux:button>
    </flux:modal.trigger>

    <flux:modal name="create-game" class="space-y-6 md:w-1/2 md:h-full">
        @if ($form->customGame != true)
            <div class="mb-2">
                <flux:heading size="lg">Search Twitch for a Game Category</flux:heading>
            </div>

            <livewire:games.search />

            @if (!is_null($searchedGame))
                <div class="flex justify-between">
                    <h5>Game found: {{ $searchedGame['name'] }}</h5>
                    <flux:button variant="primary" size="xs" icon="x-mark" wire:click="clear" />
                </div>

                <div class="space-y-4">
                    <img
                        width="142" 
                        height="190"
                        class="rounded-xl"
                        src="{{ $searchedGame['box_art_url'] }}"
                        alt="{{ $searchedGame['name'] }}"
                    />

                    <flux:separator />

                    <flux:input
                        wire:model="form.gameMode"
                        label="Game Mode"
                        description="A game type or game mode within the selected game."
                    />

                    <div class="flex justify-end mt-4">
                        <flux:button
                            variant="primary" 
                            icon="plus"
                            wire:click="store()"
                        >
                            Add to Feed
                        </flux:button>
                    </div>
                </div>
            @else
                <div class="my-2">
                    <flux:checkbox
                        wire:model.live="form.customGame"
                        label="OR add any activity"
                    />
                </div>
            @endif
        @else
            <div class="mb-2">
                <flux:heading size="lg">Add any activity you'd like</flux:heading>
            </div>

            <div class="my-2">
                <flux:checkbox
                    wire:model.live="form.customGame"
                    label="Add any activity"
                />
            </div>

            <div class="space-y-2 mt-2">
                <flux:input
                    wire:model="form.customGameName"
                    label="Activity"
                    description="A video game, tabletop game or any other activity."
                    required
                />

                <flux:input
                    wire:model="form.gameMode"
                    label="Activity Type"
                    description="A game type or game mode within the selected activity."
                />

                <div class="flex justify-end mt-4">
                    <flux:button
                        variant="primary" 
                        icon="plus"
                        wire:click="store()"
                    >
                        Add to Feed
                    </flux:button>
                </div>
            </div>
        @endif
        
    </flux:modal>
</div>
