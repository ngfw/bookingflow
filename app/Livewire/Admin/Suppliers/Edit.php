<?php

namespace App\Livewire\Admin\Suppliers;

use Livewire\Component;
use App\Models\Supplier;

class Edit extends Component
{
    public Supplier $supplier;

    public $name = '';
    public $contact_person = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';
    public $country = '';
    public $notes = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'contact_person' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'city' => 'nullable|string|max:100',
        'state' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'country' => 'nullable|string|max:100',
        'notes' => 'nullable|string|max:1000',
        'is_active' => 'boolean',
    ];

    public function mount(Supplier $supplier)
    {
        $this->supplier = $supplier;
        
        // Load existing supplier data
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->city = $supplier->city;
        $this->state = $supplier->state;
        $this->postal_code = $supplier->postal_code;
        $this->country = $supplier->country;
        $this->notes = $supplier->notes;
        $this->is_active = $supplier->is_active;
    }

    public function save()
    {
        $this->validate();

        $this->supplier->update([
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Supplier updated successfully!');
        return redirect()->route('admin.suppliers.index');
    }

    public function render()
    {
        return view('livewire.admin.suppliers.edit')->layout('layouts.admin');
    }
}
