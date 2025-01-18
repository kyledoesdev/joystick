<div>
    <div class="space-y-2">
        <div class="flex justify-between">
            <div class="w-full sm:w-auto">
                <h5 class="text-2xl lg:text-4xl font-bold">
                    Edit Group: {{ $this->group->name }}
                </h5>
            </div>
    
            <div class="flex space-x-2">
                <flux:button variant="primary" size="sm" wire:click="updateGroup">
                    Update Group
                </flux:button>
                <flux:button size="sm" variant="danger" icon-trailing="trash" wire:click="confirm({{ $group->getKey() }})">
                    Delete Group
                </flux:button>
            </div>
        </div>
    
        <flux:separator class="mb-4" />
    </div>

    <div class="space-y-8 mt-4">
        <flux:card>
            <div class="flex space-y-4">
                <div class="space-y-4">
                    <flux:input label="Group Name" wire:model.live.debounce.500ms="groupForm.name" placeholder="My Group" required />
                    <flux:separator />
                    <flux:checkbox
                        wire:model="groupForm.ownerFeedsOnly"
                        label="Only group owner (you) can create feeds?" 
                        required
                    />
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex flex-col space-y-4">
                <flux:checkbox
                    wire:model.live="groupForm.discordUpdates"
                    label="Enable discord updates?" 
                />

                @if ($this->groupForm->discordUpdates)
                    <flux:input
                        type="password"
                        label="Webhook URL"
                        description="Send feed & suggestion updates to a discord channel of your choice."
                        wire:model="groupForm.discordWebHook"
                        viewable
                    />
                @endif
            </div>
        </flux:card>

        <flux:card>
            <div class="flex justify-between mb-4">
                <div class="mt-1">
                    <flux:heading>Your group's members</flux:heading>
                </div>
                <div>
                    <flux:modal.trigger name="add-member">
                        <flux:button icon="plus" variant="filled" size="sm">Member</flux:button>
                    </flux:modal.trigger>
                </div>
            </div>

            <flux:table :paginate="count($members) ? $members : false">
                @forelse ($members as $member)
                    @if ($loop->first)
                        <flux:columns>
                            <flux:column sortable :sorted="$this->sortBy === 'name'" :direction="$this->sortDirection" wire:click="sort('name')">Name</flux:column>
                            <flux:column sortable :sorted="$this->sortBy === 'status_name'" :direction="$this->sortDirection" wire:click="sort('status_name')">Status</flux:column>
                            <flux:column></flux:column>
                        </flux:columns>
                    @endif

                    <flux:rows>
                        <flux:row :key="$member->id">
                            <flux:cell class="flex items-center gap-3">
                                <flux:avatar src="{{ $member->avatar }}" />

                                {{ $member->name }}
                            </flux:cell>

                            <flux:cell>
                                <flux:badge variant="solid" :color="$member->status_color">{{ $member->status_name ?? 'Not Invited' }}</flux:badge>
                            </flux:cell>

                            <flux:cell>
                                @if ($member->getKey() != auth()->id())
                                    <flux:button icon="trash" variant="danger" size="sm" wire:click="removeMember({{ $member->getKey() }})" />
                                @endif
                            </flux:cell>
                        </flux:row>
                    </flux:rows>
                @empty
                    <x-empty-collection :search="$search" message="No users found for term: {{ $search }}" />
                @endforelse
            </flux:table>
        </flux:card>
    </div>

    {{-- Add member modal --}}
    <flux:modal name="add-member" class="w-96">
        <div class="mb-4">
            <flux:heading>Add new members to your group</flux:heading>
        </div>

        <div class="space-y-4">
            <flux:input
                label="Search"
                placeholder="Your friend's name"
                wire:model.live.debounce.500ms="search"
                icon-trailing="magnifying-glass"
            />

            @if ($search != '' && !is_null($searchedUser))
                <flux:card>
                    <div class="flex justify-between">
                        <div class="flex items-center gap-3">
                            <flux:avatar src="{{ $searchedUser->avatar }}" />
                            {{ $searchedUser->name }}
                        </div>
                        <div>
                            <flux:button variant="primary" size="xs" icon="plus" wire:click="storeMember({{ $searchedUser->getKey() }})" />
                        </div>
                    </div>
                </flux:card>
            @elseif ($search != '' && is_null($searchedUser))
                <flux:badge>No results for a user named: {{ $search }}</flux:badge>
            @endif
        </div>
    </flux:modal>

    {{-- Destroy Confirm Modal --}}
    <flux:modal name="destroy-group" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete Group: {{ $groupForm->group?->name }}?</flux:heading>
            <flux:subheading>Are you sure you want to delete this group, it's feeds & all of it's underlying data?</flux:subheading>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="danger" size="xs" wire:click="destroy">Delete</flux:button>
        </div>
    </flux:modal>
</div>
