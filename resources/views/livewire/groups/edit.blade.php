<div>
    <x-slot name="header">Edit Group: {{ $this->group->name }}</x-slot>

    <div class="space-y-4">
        <flux:card>
            <div class="flex space-y-4">
                <div class="space-y-4">
                    <flux:input label="Group Name" wire:model.live.debounce.500ms="form.name" placeholder="My Group" required />
                    <flux:separator />
                    <flux:checkbox
                        wire:model="form.ownerFeedsOnly"
                        label="Only group owner (you) can create feeds?" 
                        required
                    />
                </div>
            </div>
        </flux:card>
            
        <div>
            <flux:heading>Add users to your group.</flux:heading>
        </div>
            
        <flux:card>
            <div class="space-y-4">
                <div class="flex">
                    <flux:input label="Search" placeholder="Your friend's name.." wire:model.live.debounce.500ms="search" icon-trailing="magnifying-glass" />
                </div>
                <flux:card>
                    <flux:table :paginate="count($this->users) ? $this->users : false">
                        @forelse ($this->users as $user)
                            @if ($loop->first)
                                <flux:columns>
                                    <flux:column sortable :sorted="$this->sortBy === 'name'" :direction="$this->sortDirection" wire:click="sort('name')">Name</flux:column>
                                    <flux:column sortable :sorted="$this->sortBy === 'status_name'" :direction="$this->sortDirection" wire:click="sort('status_name')">Status</flux:column>
                                    <flux:column></flux:column>
                                </flux:columns>
                            @endif

                            <flux:rows>
                                <flux:row :key="$user->id">
                                    <flux:cell class="flex items-center gap-3">
                                        <flux:avatar src="{{ $user->avatar }}" />

                                        {{ $user->name }}
                                    </flux:cell>

                                    <flux:cell>
                                        <flux:badge variant="solid" :color="$user->status_color">{{ $user->status_name ?? 'Not Invited' }}</flux:badge>
                                    </flux:cell>

                                    <flux:cell>
                                        <flux:checkbox.group class="mb-3" wire:model.live="form.invited_users">
                                            @if ($user->getKey() == auth()->id())
                                                <flux:checkbox value="{{ $user->getKey() }}" checked disabled />
                                            @else
                                                <flux:checkbox value="{{ $user->getKey() }}" />
                                            @endif
                                        </flux:checkbox.group>
                                    </flux:cell>
                                </flux:row>
                            </flux:rows>
                        @empty
                            <x-empty-collection :search="$search" message="No users found for term: {{ $search }}" />
                        @endforelse
                    </flux:table>
                </flux:card>
            </div>
        </flux:card>
    
        <flux:card>
            <flux:button variant="primary" wire:click="update">
                Update Group
            </flux:button>
        </flux:card>
    </div>
</div>
