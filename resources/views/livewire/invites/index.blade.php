<div>
    <x-slot name="header">Your Group Invites</x-slot>

    <flux:card>
        <flux:table>
            @forelse ($this->invites as $invite)
                @if ($loop->first)
                    <flux:columns>
                        <flux:column sortable :sorted="$sortBy === 'group_name'" :direction="$sortDirection" wire:click="sort('group_name')">Group</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'owner_name'" :direction="$sortDirection" wire:click="sort('owner_name')">Owner</flux:column>
                        <flux:column sortable :sorted="$sortBy === 'invited_at'" :direction="$sortDirection" wire:click="sort('invited_at')">Invited At</flux:column>
                        <flux:column>Accept Invite?</flux:column>
                    </flux:columns>
                @endif
                
                <flux:rows>
                    <flux:row :key="$invite->getKey()">
                        <flux:cell>{{ $invite->group_name }}</flux:cell>

                        <flux:cell>
                            <div class="flex space-x-4">
                                <flux:avatar src="{{ $invite->owner_avatar }}" />
                                <span class="mt-2">{{ $invite->owner_name }}</span>
                            </div>
                        </flux:cell>

                        <flux:cell variant="strong">{{ $invite->invited_at }}</flux:cell>

                        <flux:cell>
                            <flux:button size="xs" variant="primary" icon="check" 
                                wire:click="update({{ $invite->invite_id }}, '{{ App\Models\InviteStatus::ACCEPTED }}')" 
                            />
                            <flux:button size="xs" variant="danger" icon="x-mark"
                                wire:click="update({{ $invite->invite_id }}, '{{ App\Models\InviteStatus::ACCEPTED }}')"
                            />
                        </flux:cell>
                    </flux:row>
                </flux:rows>
            @empty
                <x-empty-table message="No invites left to review." />
            @endforelse            
        </flux:table>
    </flux:card>
</div>
