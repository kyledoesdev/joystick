<div>
    <flux:modal.trigger name="view-votes-{{ $suggestion->getKey() }}">
        <flux:button variant="primary" size="sm" icon="user-circle" wire:click="$refresh" /> {{-- refresh voters table on click of button --}}
    </flux:modal.trigger>
    
    <flux:modal name="view-votes-{{ $suggestion->getKey() }}" class="md:w-1/2 space-y-6">
        <div>
            <flux:heading size="lg">Votes for: {{ $suggestion->game->name }}</flux:heading>
            <flux:subheading>{{ $suggestion->caption }}</flux:subheading>
        </div>

        <flux:table class="my-4" :paginate="count($this->votes) ? $this->votes : false">
            <flux:columns>
                <flux:column></flux:column>
                <flux:column>User</flux:column>
                <flux:column>Vote</flux:column>
            </flux:columns>

            <flux:rows>
                @foreach ($this->votes as $vote)
                    <flux:row>
                        <flux:cell>
                            <flux:avatar src="{{ $vote->user->avatar }}" />
                        </flux:cell>
                        <flux:cell>
                            {{ $vote->user->name }}
                        </flux:cell>
                        <flux:cell>
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
                        </flux:cell>
                    </flux:row>
                @endforeach
            </flux:rows>
        </flux:table>
    </flux:modal>
</div>