<?php

use Flux\Flux;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component
{
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        auth()->user()->update([
            'password' => bcrypt($validated['password']),
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');

        Flux::toast(variant: 'success', text: 'Password Updated!', duration: 3000);
    }
}; ?>

<section class="my-4">
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form wire:submit="updatePassword" class="mt-6 space-y-6">
        <div>
            <flux:input type="password" wire:model="current_password" label="Current Password" description="Confirm your current password" required />
        </div>

        <div>
            <flux:input type="password" wire:model="password" label="New Password" description="Enter your new password" required />
        </div>

        <div>
            <flux:input type="password" wire:model="password_confirmation" label="New Password Confirmation" description="Enter your new password again." required />
        </div>

        <div class="flex items-center gap-4">
            <flux:button variant="primary" size="sm" wire:click="updatePassword">
                Save
            </flux:button>
        </div>
    </form>
</section>
