<div>
    <x-slot name="header">{{ Str::possessive($group->name) }} Feeds</x-slot>

    @if ($group->owner_feeds_only == false || ($group->owner_create_feeds == true && $group->owner_id == auth()->id()))
        <x-slot name="action">
            <flux:modal.trigger name="create-feed">
                <flux:button variant="primary" size="sm" icon="plus">Create Feed</flux:button>
            </flux:modal.trigger>
        </x-slot>
    @endif

    <flux:card>
        @forelse ($this->feeds as $feed)
            @if ($loop->first)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @endif

            <flux:card class="flex flex-col my-2" wire:key="feed-{{ $feed->getKey() }}">
                <div class="flex justify-between mb-1">
                    <flux:heading class="mt-1">{{ $feed->name }}</flux:heading>
                    
                    @if ($feed->user_id == auth()->id())
                        <div>
                            <flux:modal.trigger name="edit-feed">
                                <flux:button variant="primary" size="xs" icon="pencil-square" wire:click="edit({{ $feed->getKey() }})" />
                            </flux:modal.trigger>                            

                            <flux:modal.trigger name="destroy-feed">
                                <flux:button variant="danger" size="xs" icon="trash" wire:click="confirm({{ $feed->getKey() }})" />
                            </flux:modal.trigger>
                        </div>
                    @endif
                </div>

                <flux:separator />

                <a class="space-y-2 my-2" href="{{ route('feed', ['groupId' => $group->getKey(), 'feedId' => $feed->getKey()]) }}">
                    @if ($feed->start_time != null)
                        <div>
                            <flux:badge size="sm">
                                {{ $feed->start_time }}
                            </flux:badge>
                        </div>
                    @endif

                    <div>
                        <flux:badge icon="squares-2x2" color="sky">Games: {{ $feed->suggestions_count }}</flux:badge>
                    </div>
                    <div>
                        <flux:badge icon="star" color="amber">Total Votes: {{ $feed->votes_count }}</flux:badge>
                    </div>
                </a>
            </flux:card>

            @if ($loop->last)
                </div>
            @endif
        @empty
            <x-empty-collection message="No feeds found." />
        @endforelse
    </flux:card>

    {{-- Create Modal --}}
    <flux:modal variant="flyout" name="create-feed">
        <div class="mb-4">
            <flux:heading size="lg">Create a new Feed</flux:heading>
        </div>

        <div class="space-y-4">
            <flux:input label="Name" wire:model="createForm.name" />

            <flux:input label="Start Time" type="datetime-local" wire:model="createForm.startTime" min="{{ now()->format('Y-m-d\TH:i') }}" />
        </div>

        <div class="flex mt-4">
            <flux:spacer />

            <flux:button size="sm" variant="primary" wire:click="store">Create</flux:button>
        </div>
    </flux:modal>

    {{-- Edit Modal --}}
    <flux:modal variant="flyout" name="edit-feed">
        <div class="mb-4">
            <flux:heading size="lg">Edit Feed: {{ $this->editForm->name }}</flux:heading>
        </div>

        <div class="space-y-4">
            <flux:input label="Name" wire:model="editForm.name" />

            <flux:input label="Start Time" type="datetime-local" wire:model="editForm.startTime" min="{{ now()->format('Y-m-d\TH:i') }}" />
        </div>

        <div class="flex mt-4">
            <flux:spacer />

            <flux:button size="sm" variant="primary" wire:click="update">Update</flux:button>
        </div>
    </flux:modal>

    {{-- Destroy Confirm Modal --}}
    <flux:modal name="destroy-feed" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete Feed: {{ $editForm->feed?->name }}?</flux:heading>
            <flux:subheading>Are you sure you want to delete this feed, it's game suggestions & all of the votes?</flux:subheading>
        </div>

        <div class="flex">
            <flux:spacer />

            <flux:button type="submit" variant="danger" size="xs" wire:click="destroy">Delete</flux:button>
        </div>
    </flux:modal>
</div>