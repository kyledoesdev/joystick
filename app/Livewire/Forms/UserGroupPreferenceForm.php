<?php

namespace App\Livewire\Forms;

use App\Models\UserGroupPreference;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserGroupPreferenceForm extends Form
{
    #[Validate('string|nullable')]
    public ?string $color = null;

    public function update(UserGroupPreference $preference)
    {
        $this->validate();

        abort_if($preference->user_id != auth()->id(), 403);

        $preference->update([
            'color' => $this->color
        ]);
    }

    public function resetColor()
    {
        $this->color = null;
    }
}
