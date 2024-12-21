<div>
    <x-slot name="header">Edit Group: {{ $this->group->name }}</x-slot>

    <div class="space-y-4">
        <flux:card>
            <div class="flex space-y-4">
                <flux:input label="Group Name" wire:model.live.debounce.500ms="form.name" placeholder="My Group" required />
            </div>
        </flux:card>
            
        <div>
            <flux:heading>Add members to your group.</flux:heading>
        </div>
            
        <flux:card>
            <div class="space-y-4">
                <div class="flex">
                    <flux:input label="Search" placeholder="Your friend's name.." wire:model.live.debounce.500ms="search" icon-trailing="magnifying-glass" />
                </div>
                <flux:card>
                    <flux:table :paginate="$this->users">
                        <flux:columns>
                            <flux:column sortable :sorted="$this->sortBy === 'name'" :direction="$this->sortDirection" wire:click="sort('name')">Name</flux:column>
                            <flux:column></flux:column>
                        </flux:columns>
                
                        <flux:rows>
                            @forelse ($this->users as $user)
                                <flux:row :key="$user->id">
                                    <flux:cell class="flex items-center gap-3">
                                        <flux:avatar src="{{ $user->avatar }}" />
                
                                        {{ $user->name }}
                                    </flux:cell>
                
                                    <flux:cell>
                                        <flux:checkbox.group class="mb-3" wire:model.live="form.members">
                                            <flux:checkbox value="{{ $user->getKey() }}" />
                                        </flux:checkbox.group>
                                    </flux:cell>
                                </flux:row>
                            @empty
                                <span>No users found.</span>
                            @endforelse
                        </flux:rows>
                    </flux:table>
                </flux:card>
            </div>
        </flux:card>
    
        <flux:card>
            @if (!empty($this->form->members))
                <flux:button variant="primary" wire:click="update">
                    Update Group
                </flux:button>
            @else
                <flux:button variant="outline" style="cursor: not-allowed;">
                    Update Group
                </flux:button>
            @endif  
        </flux:card>
    </div>
</div>
