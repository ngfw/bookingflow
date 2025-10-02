<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.staff')]
class Performance extends Component
{
    public function render()
    {
        return view('livewire.staff.performance');
    }
}