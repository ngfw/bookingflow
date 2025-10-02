<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.staff')]
class Clients extends Component
{
    public function render()
    {
        return view('livewire.staff.clients');
    }
}