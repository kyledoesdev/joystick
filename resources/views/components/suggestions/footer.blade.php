@props(['suggestion'])

<div class="flex justify-between mt-2 gap-x-1">
    <div>
        @if ($suggestion->game_mode)
            <flux:badge>{{ $suggestion->game_mode }}</flux:badge>
        @endif
    </div>
    <div>
        @if ($suggestion->user_id == auth()->id())
            <livewire:games.edit
                :suggestion="$suggestion"
                wire:key="edit-fields-{{ $suggestion->getKey() }}"
            />
        @endif
    </div>
</div>