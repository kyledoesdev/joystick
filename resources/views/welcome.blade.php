<x-guest-layout>
    <flux:card class="flex flex-col">
        <div class="flex justify-center text-center">
            <flux:heading class="mb-4">Want an easier way to decide on games to play with your friends?</flux:heading>
        </div>

        <div class="flex justify-center">
            <flux:button icon-trailing="arrow-up-right" variant="primary" href="{{ route('twitch.login') }}">
                Login with Twitch
            </flux:button>
        </div>
    </flux:card>
</x-guest-layout>