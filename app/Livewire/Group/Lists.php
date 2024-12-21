<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\ListForm;
use App\Models\Group;
use App\Models\GroupList;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Lists extends Component
{
    public Group $group;

    public ListForm $createForm;
    public ListForm $editForm;

    public function mount()
    {
        $this->group = Group::with('lists')->findOrFail(request()->id);
    }

    public function render()
    {
        return view('livewire.lists.index');
    }

    #[Computed]
    public function lists()
    {
        return $this->group->lists()->withCount('suggestions', 'votes')->orderBy('start_time', 'desc')->get();
    }

    public function store()
    {
        $this->createForm->store($this->group);
    }

    public function edit($listId)
    {
        $this->editForm->edit($listId);
    }

    public function update()
    {
        $this->editForm->update();
    }
}
