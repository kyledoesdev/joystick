<div>
    <flux:modal.trigger name="edit-game-{{ $suggestion->getKey() }}">
        <flux:button variant="primary" size="sm" icon="pencil-square" wire:click="edit"></flux:button>
    </flux:modal.trigger>

    <flux:modal.trigger name="confirm-game-{{ $suggestion->getKey() }}">
        <flux:button variant="danger" size="sm" icon="trash"></flux:button>
    </flux:modal.trigger>

    <flux:modal variant="flyout" name="edit-game-{{ $suggestion->getKey() }}">
        <div class="my-4">
            <flux:heading size="lg">Edit Game: {{ $suggestion->game->name }}</flux:heading>
        </div>

        <div class="my-1 mx-1">
            @if (! $suggestion->game->is_custom)
                <img
                    width="142" 
                    height="190"
                    class="rounded-xl my-2"
                    src="{{ $suggestion->game->cover }}"
                    alt="{{ $suggestion->game->name }}"
                />
            @endif

            <flux:separator />

            <div class="mt-4 space-y-2">
                @if ($suggestion->game->is_custom)
                    <flux:input
                        wire:model="form.customGameName"
                        label="Activity"
                        description="A video game, tabletop game or any other activity."
                        required
                    />
                @endif

                <flux:input
                    wire:model="form.gameMode"
                    label="Game Mode"
                    description="A game type or game mode within the selected game."
                />
            </div>
        </div>

        <div class="flex mt-4">
            <flux:spacer />

            <flux:button type="submit" variant="primary" wire:click="update">Save changes</flux:button>
        </div>
    </flux:modal>

    <flux:modal name="confirm-game-{{ $suggestion->getKey() }}">
        <div class="space-y-4">
            <div>
                <flux:heading size="lg">Delete Game {{ $suggestion->game->name }}?</flux:heading>
                <flux:subheading>Are you sure you to delete this game suggestion?</flux:subheading>
            </div>
    
            <div class="flex my-4">
                <flux:spacer />
    
                <flux:button type="submit" variant="danger" size="sm" wire:click="destroy">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>