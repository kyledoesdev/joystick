<div>
    <form wire:submit="search">
        <flux:input.group>
            <flux:input wire:model="phrase" placeholder="Minecraft" required />

            <flux:button type="submit" icon="magnifying-glass"></flux:button>
        </flux:input.group>
    </form>

    <flux:separator />
</div>