<?php

namespace App\Livewire\Forms;

use App\Actions\Groups\StoreGroup;
use App\Models\Group;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithPagination;

class GroupForm extends Form
{
    #[Validate('required|string|min:3|max:36')]
    public string $name = '';

    #[Validate('required|boolean')]
    public bool $ownerFeedsOnly = false;

    #[Validate('nullable|string|max:255')]
    public $discordWebHook = null;

    #[Validate('nullable|boolean')]
    public $discordUpdates = false;

    public ?Group $group = null;

    public function store()
    {
        $this->validate();

        (new StoreGroup)->handle(auth()->user(), [
            'name' => $this->name,
            'discord_webhook_url' => $this->discordWebHook,
            'discord_updates' => $this->discordUpdates,
            'owner_feeds_only' => $this->ownerFeedsOnly
        ]);

        $this->reset();

        Flux::modal('create-group')->close();
        Flux::toast(variant: 'success', text: 'Group Created - Go Invite your friends!', duration: 3000);
    }

    public function edit($group)
    {
        $this->group = $group;
        $this->name = $this->group->name;
        $this->ownerFeedsOnly = $this->group->owner_feeds_only;
        $this->discordWebHook = $this->group->discord_webhook_url;
        $this->discordUpdates = $this->group->discord_updates;
    }

    public function update($group)
    {
        $this->validate();

        $this->group->update([
            'name' => $this->name,
            'owner_feeds_only' => $this->ownerFeedsOnly,
            'discord_webhook_url' => $this->discordWebHook,
            'discord_updates' => $this->discordUpdates,
        ]);
        
        Flux::toast(variant: 'success', text: 'Group Updated!', duration: 3000);
    }

    public function confirm($groupId)
    {
        $this->group = Group::findOrFail($groupId);

        Flux::modal('destroy-group')->show();
    }

    public function destroy()
    {
        abort_if($this->group->owner_id != auth()->id(), 403);

        $this->group->delete();
    }
}
