<?php

namespace App\Livewire\Admin;

use App\Models\MenuItem;
use Livewire\Component;

class MenuManager extends Component
{
    public $menuItems;
    public $selectedLocation = 'header';
    public $editingId = null;

    public $label;
    public $url;
    public $route;
    public $order;
    public $target = '_self';
    public $is_active = true;
    public $show_when_logged_in = null;

    protected $rules = [
        'label' => 'required|string|max:255',
        'url' => 'nullable|string',
        'route' => 'nullable|string',
        'order' => 'required|integer',
        'target' => 'required|in:_self,_blank',
        'is_active' => 'boolean',
        'show_when_logged_in' => 'nullable|boolean',
    ];

    public function mount()
    {
        $this->loadMenuItems();
    }

    public function loadMenuItems()
    {
        $this->menuItems = MenuItem::where('location', $this->selectedLocation)
            ->orderBy('order')
            ->get();
    }

    public function updatedSelectedLocation()
    {
        $this->loadMenuItems();
        $this->resetForm();
    }

    public function edit($id)
    {
        $menuItem = MenuItem::find($id);
        $this->editingId = $id;
        $this->label = $menuItem->label;
        $this->url = $menuItem->url;
        $this->route = $menuItem->route;
        $this->order = $menuItem->order;
        $this->target = $menuItem->target;
        $this->is_active = $menuItem->is_active;
        $this->show_when_logged_in = $menuItem->show_when_logged_in;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'label' => $this->label,
            'url' => $this->url,
            'route' => $this->route,
            'order' => $this->order,
            'target' => $this->target,
            'location' => $this->selectedLocation,
            'is_active' => $this->is_active,
            'show_when_logged_in' => $this->show_when_logged_in,
        ];

        if ($this->editingId) {
            MenuItem::find($this->editingId)->update($data);
            session()->flash('message', 'Menu item updated successfully!');
        } else {
            MenuItem::create($data);
            session()->flash('message', 'Menu item created successfully!');
        }

        $this->resetForm();
        $this->loadMenuItems();
    }

    public function delete($id)
    {
        MenuItem::find($id)->delete();
        session()->flash('message', 'Menu item deleted successfully!');
        $this->loadMenuItems();
    }

    public function resetForm()
    {
        $this->editingId = null;
        $this->label = '';
        $this->url = '';
        $this->route = '';
        $this->order = $this->menuItems->count() + 1;
        $this->target = '_self';
        $this->is_active = true;
        $this->show_when_logged_in = null;
    }

    public function render()
    {
        return view('livewire.admin.menu-manager')->layout('layouts.admin');
    }
}
