<x-guest-layout>
    <flux:card class="flex justify-between">
        <flux:heading class="mt-3">Log in with twitch to start the fun 🚀</flux:heading>
        
        <flux:button icon-trailing="arrow-up-right" variant="primary" href="{{ route('twitch.login') }}">
            Login
        </flux:button>
    </flux:card>
</x-guest-layout>