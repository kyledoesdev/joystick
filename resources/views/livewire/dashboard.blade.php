<div>
    <x-slot name="header">Your Groups</x-slot>

    <x-slot name="action">
        <flux:modal.trigger name="create-group">
            <flux:button variant="primary" icon="plus" size="sm">Create New Group</flux:button>
        </flux:modal.trigger>
    </x-slot>

    <flux:card>
        @forelse ($this->groups as $group)
            @if ($loop->first)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @endif

            @php
                $preference = $group->userPreferences->where('user_id', auth()->id())->first();
                $color = $preference->color ?? '';
            @endphp

            <flux:card class="flex flex-col space-y-2 my-2" style="background-color: {{ $color }};">
                <div class="flex justify-between">
                    <flux:heading class="mt-1">{{ $group->name }}</flux:heading>

                    <div class="flex">
                        <livewire:group.preference :key="$preference->getKey()" :preference="$preference" />

                        @if ($group->owner_id != auth()->id())
                            <flux:modal.trigger name="update-group-invite">
                                <flux:button class="ml-2" size="xs" variant="danger" icon="x-circle" wire:click="confirmLeaveGroup({{ $group->getKey() }})" />
                            </flux:modal.trigger>
                        @endif
                    </div>
                </div>

                <flux:separator />

                <a href="{{ route('group', $group) }}">
                    <div class="space-y-2">
                        <div>
                            <flux:badge icon="user-circle" color="lime">Users: {{ $group->invites_count }}</flux:badge>
                        </div>
                        <div>
                            <flux:badge icon="squares-2x2" color="sky">Feeds: {{ $group->feeds_count }}</flux:badge>
                        </div>
                        <div>
                            {{-- Todo fix this with hasmanydeep --}}
                            <flux:badge icon="star" color="amber">Total Votes: 
                                {{
                                    count($group->feeds->flatMap(function ($feed) {
                                        return $feed->suggestions->flatMap(function ($suggestion) {
                                            return $suggestion->votes;
                                        });
                                    }))
                                }}
                            </flux:badge>
                        </div>
                    </div>
                </a>
            </flux:card>

            @if ($loop->last)
                </div>
            @endif
        @empty
            <x-empty-collection message="You are not in any groups." />
        @endforelse
    </flux:card>

    {{-- Create Group Modal (basic info only) --}}
    <flux:modal variant="flyout" name="create-group">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Create Group</flux:heading>
            </div>
    
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
    
            <div class="flex">
                <flux:spacer />
    
                <flux:button type="submit" variant="primary" size="xs" wire:click="store">Create</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Leave group Confirm Modal --}}
    <flux:modal name="update-group-invite" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Leave Group {{ $inviteForm?->invite?->group?->name }}?</flux:heading>
            <flux:subheading>Are you sure you want to leave this group?</flux:subheading>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="danger" size="xs" wire:click="leaveGroup({{ $inviteForm->invite?->getKey() }})">Leave</flux:button>
        </div>
    </flux:modal>
</div>