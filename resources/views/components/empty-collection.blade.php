@props([
    'search' => '',
    'message' => 'No Results'
])

<div class="flex justify-center items-center">
    <flux:badge variant="solid">
        @if ($search != '')
            No matches found for: {{ $search }}
        @else
            {{ $message }}
        @endif
    </flux:badge>
</div>