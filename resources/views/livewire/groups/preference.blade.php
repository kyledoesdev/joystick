<div>
    <flux:modal.trigger name="edit-prefs-{{ $preference->getKey() }}">
        <flux:button variant="primary" size="xs" icon="wrench-screwdriver"></flux:button>
    </flux:modal.trigger>

    <flux:modal name="edit-prefs-{{ $preference->getKey() }}" class="md:w-1/2 space-y-6">
        <div>
            <flux:heading size="lg">Update Preferences</flux:heading>
            <flux:subheading>Update your preferences for {{ $preference->group->name }}</flux:subheading>
        </div>

        <flux:separator class="my-4" />

        <div class="flex justify-between space-x-2">
            <flux:input
                type="color"
                label="Color"
                description="Note: custom colors do not adjust to light/dark mode"
                wire:model="form.color"
            />

            <flux:button size="sm" icon="x-mark" wire:click="resetColor" />
        </div>

        <flux:separator />

        <div class="flex">
            <flux:spacer />

            <flux:button size="sm" variant="primary" wire:click="update">Save</flux:button>
        </div>
    </flux:modal>
</div>
