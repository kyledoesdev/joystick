<?php

namespace App\Livewire\Forms;

use App\Models\Feed;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Attributes\Validate;
use Livewire\Form;

class FeedForm extends Form
{
    #[Validate('required|string|min:3|max:36')]
    public string $name = '';

    public $startTime = null;

    public ?int $feedId = null;

    public function store($group)
    {
        $this->validate();

        $group->feeds()->create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'start_time' => $this->startTime != null
                ? Carbon::parse($this->startTime, auth()->user()->timezone)->tz('UTC')
                : null,
        ]);

        $this->reset();

        Flux::modal('create-feed')->close();
        Flux::toast(variant: 'success', text: 'Feed Created!', duration: 3000);
    }

    public function edit($feedId)
    {
        $feed = Feed::findOrFail($feedId);

        $this->feedId = $feedId;
        $this->name = $feed->name;
        $this->startTime = $feed->start_time != null
            ? Carbon::parse($feed->start_time)->format('Y-m-d\TH:i')
            : null;
    }

    public function update()
    {
        $this->validate();

        Feed::findOrFail($this->feedId)->update([
            'name' => $this->name,
            'start_time' => $this->startTime != null
                ? Carbon::parse($this->startTime, auth()->user()->timezone)->tz('UTC')
                : null
        ]);

        Flux::modal("edit-feed")->close();
        Flux::toast(variant: 'success', text: 'Feed Updated!', duration: 3000);
    }
}
