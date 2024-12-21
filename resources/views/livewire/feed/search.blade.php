<div>
    <flux:input
        icon-trailing="magnifying-glass"
        placeholder="Search suggested games"
        wire:model.live.debounce.500ms="search"
    />
</div>