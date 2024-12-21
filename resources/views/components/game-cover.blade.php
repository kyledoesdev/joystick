@props(['game'])

<img
    class="rounded-xl"
    width="142" height="190"
    src="{{ $game['cover'] }}"
    alt="{{ $game['name'] }}"
/>