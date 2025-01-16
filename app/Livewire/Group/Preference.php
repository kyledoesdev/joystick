<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\UserGroupPreferenceForm;
use App\Models\Group;
use Flux\Flux;
use Livewire\Component;

class Preference extends Component
{
    public Group $group;
    public UserGroupPreferenceForm $form;

    public function mount()
    {   
        $this->form->color = $this->group->userPreferences->where('user_id', auth()->id())->first()->color;
    }

    public function render()
    {
        return view('livewire.groups.preference');
    }

    public function update()
    {
        $this->form->update($this->group);

        $this->dispatch('user-preferences-updated');

        Flux::toast(variant: 'success', text: 'Preferences Updated!', duration: 3000);
    }

    public function resetColor()
    {
        $this->form->color = null;
        $this->form->update($this->group);

        $this->dispatch('user-preferences-updated');
    }
}
