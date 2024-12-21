<?php

namespace App\Livewire\Forms;

use App\Models\GroupList;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ListForm extends Form
{
    #[Validate('required|string|min:3|max:36')]
    public string $name = '';

    public $startTime = null;

    public ?int $listId = null;

    public function store($group)
    {
        $this->validate();

        $group->lists()->create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'start_time' => $this->startTime != null
                ? Carbon::parse($this->startTime, auth()->user()->timezone)->tz('UTC')
                : null,
        ]);

        $this->reset();

        Flux::modal('create-list')->close();
        Flux::toast(variant: 'success', text: 'List Created!', duration: 3000);
    }

    public function edit($listId)
    {
        $list = GroupList::findOrFail($listId);

        $this->listId = $listId;
        $this->name = $list->name;
        $this->startTime = $list->start_time != null
            ? Carbon::parse($list->start_time)->format('Y-m-d\TH:i')
            : null;
    }

    public function update()
    {
        $this->validate();

        GroupList::findOrFail($this->listId)->update([
            'name' => $this->name,
            'start_time' => $this->startTime != null
                ? Carbon::parse($this->startTime, auth()->user()->timezone)->tz('UTC')
                : null
        ]);

        Flux::modal("edit-list")->close();
        Flux::toast(variant: 'success', text: 'List Updated!', duration: 3000);
    }
}
