<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\GroupForm;
use App\Livewire\Traits\TableHelpers;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CreateGroup extends Component
{
    use TableHelpers;
    use WithPagination;

    public GroupForm $form;
    
    public function render()
    {
        return view('livewire.groups.create');
    }

    public function store()
    {
        $this->form->store();

        session()->flash('success', 'Group created successfully.');

        $this->redirect(route('dashboard'));
    }

    #[Computed]
    public function users()
    {
        return User::forGroupFormTable($this->search, $this->sortBy, $this->sortDirection);
    }
}
