<div>
    <x-slot name="header">Your Group Invites</x-slot>

    <flux:card>
        <flux:table>
            @forelse ($this->invites as $invite)
                @if ($loop->first)
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortBy === 'group_name'" :direction="$sortDirection" wire:click="sort('group_name')">Group</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'owner_name'" :direction="$sortDirection" wire:click="sort('owner_name')">Owner</flux:table.column>
                        <flux:table.column sortable :sorted="$sortBy === 'invited_at'" :direction="$sortDirection" wire:click="sort('invited_at')">Invited At</flux:table.column>
                        <flux:table.column>Accept Invite?</flux:table.column>
                    </flux:table.columns>
                @endif
                
                <flux:table.rows>
                    <flux:table.row :key="$invite->getKey()">
                        <flux:table.cell>{{ $invite->group_name }}</flux:table.cell>

                        <flux:table.cell>
                            <div class="flex space-x-4">
                                <flux:avatar src="{{ $invite->owner_avatar }}" />
                                <span class="mt-2">{{ $invite->owner_name }}</span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell variant="strong">{{ $invite->invited_at }}</flux:table.cell>

                        <flux:table.cell>
                            <flux:button size="xs" variant="primary" icon="check" 
                                wire:click="update({{ $invite->getKey() }}, '{{ App\Models\InviteStatus::ACCEPTED }}')" 
                            />
                            <flux:button size="xs" variant="danger" icon="x-mark"
                                wire:click="update({{ $invite->getKey() }}, '{{ App\Models\InviteStatus::DECLINED }}')"
                            />
                        </flux:table.cell>
                    </flux:table.row>
                </flux:table.rows>
            @empty
                <x-empty-collection message="No invites left to review." />
            @endforelse            
        </flux:table>
    </flux:card>
</div>
