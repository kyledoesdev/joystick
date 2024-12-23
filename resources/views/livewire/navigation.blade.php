<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        session()->flash('success', 'You have logged out, see ya next time!');

        $this->redirect('/', navigate: true);
    }
}; ?>

<flux:header container>
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="home" href="{{ route('welcome') }}" :current="request()->is('/')">
            Home
        </flux:navbar.item>

        @auth
            <flux:navbar.item icon="adjustments-horizontal" href="{{ route('dashboard') }}" :current="request()->is('dashboard')">
                Dashboard
            </flux:navbar.item>

            <flux:navbar.item
                href="{{ route('invites') }}"
                icon="envelope" 
                :current="request()->is('invites')"
            >
                <livewire:invites.navigation-badge />
            </flux:navbar.item>
        @endauth

    </flux:navbar>

    <flux:spacer />

    <flux:button x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" />

    @auth
    
        <flux:dropdown position="top" align="start">
            <flux:profile avatar="{{ auth()->user()->avatar }}" />

            <flux:menu>
                <flux:menu.item icon="arrow-right-start-on-rectangle" wire:click="logout">
                    Logout
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>
    @else
        <flux:button size="sm" class="mx-1" href="{{ route('twitch.login') }}" icon-trailing="arrow-up-right">Login</flux:button>
    @endauth
</flux:header>