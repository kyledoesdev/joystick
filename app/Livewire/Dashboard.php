<?php

namespace App\Livewire;

use App\Livewire\Forms\GroupForm;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    public GroupForm $form;

    public function render()
    {
        return view('livewire.dashboard');
    }

    #[Computed]
    public function groups()
    {
        /* todo fix & eager load votes hasManyDeep */
        return auth()->user()->groups()->with('lists.suggestions.votes')->withCount(['members', 'lists'])->get();
    }

    public function confirm($groupId)
    {
        $this->form->confirm($groupId);
    }

    public function destroy()
    {
        $this->form->destroy();
    }
}
