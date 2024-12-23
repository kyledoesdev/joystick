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

    public ?Feed $feed = null;

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
        $this->feed = Feed::findOrFail($feedId);

        $this->name = $this->feed->name;
        $this->startTime = $this->feed->start_time != null
            ? Carbon::parse($this->feed->start_time)->format('Y-m-d\TH:i')
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

    public function confirm($feedId)
    {
        $this->feed = Feed::findOrFail($feedId);

        Flux::modal('destroy-feed')->show();
    }

    public function destroy()
    {
        $this->feed->delete();

        Flux::modal('destroy-feed')->close();
        Flux::toast(variant: 'success', text: 'Feed Deleted!', duration: 3000);
    }
}
