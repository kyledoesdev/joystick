<div>
    <x-slot name="header">Your Groups</x-slot>

    <flux:card>
        <div class="flex justify-end">
            <flux:button href="{{ route('group.create') }}" variant="primary" icon="plus" size="sm">Create New Group</flux:button>
        </div>

        <flux:separator class="my-4" />

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->groups as $group)
                <flux:card class="flex flex-col space-y-2 my-2">
                    <div class="flex justify-between mx-2 mb-2">
                        <div>
                            <h5 class="underline">{{ $group->name }}</h5> 
                        </div>

                        @if ($group->owner_id == auth()->id())
                            <div>
                                <flux:button 
                                    href="{{ route('group.edit', ['id' => $group->getKey()]) }}"
                                    size="xs"
                                    variant="primary"
                                    icon="pencil-square"
                                />
                                <flux:button size="xs" variant="danger" icon="trash" wire:click="confirm({{ $group->getKey() }})" />
                            </div>
                        @else
                            leave group
                        @endif
                    </div>

                    <a href="{{ route('group', ['groupId' => $group->getKey()]) }}">
                        <div class="space-y-2">
                            <div class="mx-2">
                                <flux:badge icon="user-circle" color="lime">Users: {{ $group->invites_count }}</flux:badge>
                            </div>
                            <div class="mx-2">
                                <flux:badge icon="squares-2x2" color="sky">Feeds: {{ $group->feeds_count }}</flux:badge>
                            </div>
                            <div class="mx-2">
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
            @endforeach
        </div>
    </flux:card>

    {{-- Destroy Confirm Modal --}}
    <flux:modal name="destroy-group" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete Group: {{ $form->group?->name }}?</flux:heading>
            <flux:subheading>Are you sure you want to delete this group, it's feeds & all of it's underlying data?</flux:subheading>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="danger" size="xs" wire:click="destroy">Delete</flux:button>
        </div>
    </flux:modal>
</div>