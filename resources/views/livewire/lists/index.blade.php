<div>
    <x-slot name="header">{{ Str::possessive($group->name) }} Lists</x-slot>

    <flux:card class="my-4">
        <div class="flex justify-end">
            <flux:modal.trigger name="create-list">
                <flux:button variant="primary" size="sm" icon="plus">Create List</flux:button>
            </flux:modal.trigger>
        </div>
    </flux:card>

    <flux:card>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($this->lists as $list)
                <flux:card
                    class="flex flex-col my-4"
                    wire:key="list-{{ $list->getKey() }}"
                >
                    <div class="flex justify-between mx-2 mb-2">
                        <div>
                            <h5 class="underline">{{ $list->name }}</h5> 
                        </div>
                        
                        @if ($list->user_id == auth()->id())
                            <div>
                                <flux:modal.trigger name="edit-list">
                                    <flux:button variant="primary" size="xs" icon="pencil-square" wire:click="edit({{ $list->getKey() }})" />
                                </flux:modal.trigger>                            

                                <flux:button variant="danger" size="xs" icon="trash" />
                            </div>
                        @endif
                    </div>

                    <a class="mx-2 space-y-4" href="{{ route('feed', ['id' => $list->getKey()]) }}">
                        @if ($list->start_time != null)
                            <div>
                                <flux:badge size="sm">
                                    {{ $list->start_time }}
                                </flux:badge>
                            </div>
                        @endif

                        <div>
                            <flux:badge icon="squares-2x2" color="sky">Games: {{ $list->suggestions_count }}</flux:badge>
                        </div>
                        <div>
                            <flux:badge icon="star" color="amber">Total Votes: {{ $list->votes_count }}</flux:badge>
                        </div>
                    </a>
                </flux:card>
            @endforeach
        </div>
    </flux:card>

    {{-- Create Modal --}}
    <flux:modal variant="flyout" name="create-list">
        <div class="mb-4">
            <flux:heading size="lg">Create a new List</flux:heading>
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
    <flux:modal variant="flyout" name="edit-list">
        <div class="mb-4">
            <flux:heading size="lg">Edit List: {{ $this->editForm->name }}</flux:heading>
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