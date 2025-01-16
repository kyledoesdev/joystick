<?php

namespace App\Livewire\Forms;

use App\Models\Group;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserGroupPreferenceForm extends Form
{
    #[Validate('string|nullable')]
    public ?string $color = null;

    public function update(Group $group)
    {
        $this->validate();

        $group->userPreferences()->where('user_id', auth()->id())->update([
            'color' => $this->color
        ]);
    }

    public function resetColor()
    {
        $this->color = null;
    }
}
