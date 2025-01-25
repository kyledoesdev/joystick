@props(['suggestion'])

<div class="flex justify-between space-x-4">
    <flux:badge>Suggested by: {{ $suggestion->user->name }}</flux:badge>
    <livewire:suggestions.voters-table
        :suggestion="$suggestion"
        wire:key="vote-table-{{ $suggestion->getKey() }}"
    />
</div>

<flux:separator class="mt-1" />