<?php

namespace App\Livewire\Forms;

use App\Livewire\Traits\TableHelpers;
use App\Models\Group;
use App\Models\GroupList;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Livewire\WithPagination;

class GroupForm extends Form
{
    #[Validate('required|string|min:3|max:36')]
    public string $name = '';

    #[Validate('required')]
    public array $members = [];

    public ?Group $group = null;

    public function store()
    {
        $this->validate();

        $group = Group::create([
            'name' => $this->name,
            'owner_id' => auth()->id()
        ]);

        GroupList::create([
            'group_id' => $group->getKey(),
            'user_id' => auth()->id(),
            'name' => Str::possessive($group->name) . ' Game Backlog'
        ]);

        foreach(collect($this->members) as $member) {
            $group->members()->attach($member, [
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function edit($group)
    {
        $this->name = $group->name;
        $this->members = $group->members->pluck('id')->toArray();
    }

    public function update($group)
    {
        $this->validate();

        $group->update(['name' => $this->name]);
        
        $currentMembers = $group->members()->whereIn('users.id', $this->members)->get();

        foreach ($this->members as $memberId) {
            $existingMember = $currentMembers->firstWhere('id', $memberId);

            $existingMember
                ? $sync[$memberId] = [
                    'updated_at' => now()
                ] 
                : $sync[$memberId] = [
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
        }

        $group->members()->sync($sync);
    }

    public function confirm($groupId)
    {
        $this->group = Group::findOrFail($groupId);

        Flux::modal('destroy-group')->show();
    }

    public function destroy()
    {
        $this->group->delete();

        Flux::modal('destroy-group')->close();
        Flux::toast(variant: 'success', text: 'Group Deleted!', duration: 3000);
    }
}
