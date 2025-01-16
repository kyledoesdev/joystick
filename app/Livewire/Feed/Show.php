<?php

namespace App\Livewire\Feed;

use App\Livewire\Forms\FeedForm;
use App\Models\Feed;
use App\Models\Group;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Show extends Component
{
    public Group $group;

    public FeedForm $createForm;
    public FeedForm $editForm;

    public function render()
    {
        return view('livewire.feed.index');
    }

    #[Computed]
    public function feeds()
    {
        return $this->group->feeds()->withCount('suggestions', 'votes')->orderBy('start_time', 'desc')->get();
    }

    public function store()
    {
        $this->createForm->store($this->group);
    }

    public function edit($feedId)
    {
        $this->editForm->edit($feedId);
    }

    public function update()
    {
        $this->editForm->update();
    }

    public function confirm($feedId)
    {
        $this->editForm->confirm($feedId);
    }

    public function destroy()
    {
        $this->editForm->destroy();
    }
}
