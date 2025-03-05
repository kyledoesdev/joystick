<div>
    <flux:modal.trigger name="view-votes-{{ $suggestion->getKey() }}">
        <flux:tooltip content="See all votes">
            <flux:button variant="primary" size="sm" icon="user-circle" wire:click="$refresh" /> {{-- refresh voters table on click of button --}}
        </flux:tooltip>
    </flux:modal.trigger>
    
    <flux:modal name="view-votes-{{ $suggestion->getKey() }}" class="md:w-1/2 space-y-6">
        <div>
            <flux:heading size="lg">Votes for: {{ $suggestion->game->name }}</flux:heading>
            <flux:subheading>{{ $suggestion->caption }}</flux:subheading>
        </div>

        <flux:table class="my-4" :paginate="count($this->votes) ? $this->votes : false">
            <flux:table.columns>
                <flux:table.column></flux:table.column>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Vote</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->votes as $vote)
                    <flux:table.row>
                        <flux:table.cell>
                            <flux:avatar src="{{ $vote->user->avatar }}" />
                        </flux:table.cell>
                        <flux:table.cell>
                            {{ $vote->user->name }}
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($vote->vote == App\Models\Vote::UP_VOTE)
                                <flux:badge color="lime">
                                    <flux:icon.arrow-up-circle />
                                </flux:badge>
                            @elseif($vote->vote == App\Models\Vote::NEUTRAL)
                                <flux:badge>
                                    <flux:icon.minus-circle />
                                </flux:badge>
                            @else
                                <flux:badge color="red">
                                    <flux:icon.arrow-down-circle />
                                </flux:badge>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:modal>
</div>