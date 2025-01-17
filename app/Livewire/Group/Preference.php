<?php

namespace App\Livewire\Group;

use App\Livewire\Forms\UserGroupPreferenceForm;
use App\Models\UserGroupPreference;
use Flux\Flux;
use Livewire\Component;

class Preference extends Component
{
    public UserGroupPreference $preference;
    public UserGroupPreferenceForm $form;

    public function mount()
    {
        $this->form->color = $this->preference->color;
    }

    public function render()
    {
        return view('livewire.groups.preference');
    }

    public function update()
    {
        $this->form->update($this->preference);

        $this->dispatch('user-preferences-updated');

        Flux::toast(variant: 'success', text: 'Preferences Updated!', duration: 3000);
    }

    public function resetColor()
    {
        $this->form->color = null;
        $this->form->update($this->preference);

        $this->dispatch('user-preferences-updated');
    }
}
