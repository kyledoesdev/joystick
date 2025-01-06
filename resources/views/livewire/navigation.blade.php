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
        @auth
            <flux:navbar.item icon="adjustments-horizontal" href="{{ route('dashboard') }}" :current="request()->is('dashboard')">
                Groups
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

    <flux:sidebar stashable sticky class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <flux:navlist>
            @auth
                <flux:navlist.item icon="adjustments-horizontal" href="{{ route('dashboard') }}" :current="request()->is('dashboard')">
                    Groups
                </flux:navlist.item>

                <flux:navlist.item icon="envelope" href="{{ route('invites') }}" :current="request()->is('invites')">
                    Invitations
                </flux:navlist.item>
            @endauth
        </flux:navlist>

        <flux:spacer />

        <flux:button x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" />

        @auth
            <flux:dropdown position="top" align="start">
                <flux:button class="mx-2" size="sm" icon-trailing="chevron-down">{{ auth()->user()->name }}</flux:button>

                <flux:menu>
                    <flux:menu.item icon="arrow-right-start-on-rectangle" wire:click="logout">
                        Logout
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        @endauth
    </flux:sidebar>
</flux:header>