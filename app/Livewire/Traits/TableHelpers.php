<?php

namespace App\Livewire\Traits;

trait TableHelpers
{
    public $sortBy = 'id';
    public $sortDirection = 'desc';
    public string $search = '';

    public function sort($column) {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }
}