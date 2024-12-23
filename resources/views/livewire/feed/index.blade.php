<div>
    <x-slot name="header">{{ Str::possessive($group->name) }} Feeds</x-slot>

    @if ($group->owner_feeds_only == false || ($group->owner_create_feeds == true && $group->owner_id == auth()->id()))
        <flux:card class="my-4">
            <div class="flex justify-end">
                <flux:modal.trigger name="create-feed">
                    <flux:button variant="primary" size="sm" icon="plus">Create Feed</flux:button>
                </flux:modal.trigger>
            </div>
        </flux:card>
    @endif

    <flux:card>
        <div :class="count($this->feeds) ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4' : ''">
            @forelse ($this->feeds as $feed)
                <flux:card
                    class="flex flex-col my-4"
                    wire:key="feed-{{ $feed->getKey() }}"
                >
                    <div class="flex justify-between mx-2 mb-2">
                        <div>
                            <h5 class="underline">{{ $feed->name }}</h5> 
                        </div>
                        
                        @if ($feed->user_id == auth()->id())
                            <div>
                                <flux:modal.trigger name="edit-feed">
                                    <flux:button variant="primary" size="xs" icon="pencil-square" wire:click="edit({{ $feed->getKey() }})" />
                                </flux:modal.trigger>                            

                                <flux:button variant="danger" size="xs" icon="trash" />
                            </div>
                        @endif
                    </div>

                    <a class="mx-2 space-y-4" href="{{ route('feed', ['groupId' => $group->getKey(), 'feedId' => $feed->getKey()]) }}">
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
            @empty
                <x-empty-table message="No feeds have been created for this group." />
            @endforelse
        </div>
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
</div>