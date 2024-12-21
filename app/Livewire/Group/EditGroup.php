<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\GroupForm;
use App\Livewire\Traits\TableHelpers;
use App\Models\Group;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class EditGroup extends Component
{
    use TableHelpers;
    use WithPagination;

    public GroupForm $form;
    public Group $group;
    
    public function mount()
    {
        $this->group = Group::with('invites')->findOrFail(request()->id);
        $this->form->edit($this->group);
    }

    public function render()
    {
        return view('livewire.groups.edit');
    }

    public function update()
    {
        $this->form->update($this->group);

        session()->flash('success', 'Group updated successfully.');

        $this->redirect(route('dashboard'));
    }

    #[Computed]
    public function users()
    {
        return User::forGroupFormTable($this->search, $this->sortBy, $this->sortDirection);
    }
}
