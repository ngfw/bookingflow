<?php

namespace App\Livewire\Admin\Clients;

use Livewire\Component;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Create extends Component
{
    // User fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $phone = '';
    public $address = '';
    public $date_of_birth = '';
    public $gender = '';

    // Client specific fields
    public $preferences = [];
    public $allergies = '';
    public $medical_conditions = '';
    public $preferred_contact = 'email';
    public $notes = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'allergies' => 'nullable|string|max:1000',
        'medical_conditions' => 'nullable|string|max:1000',
        'preferred_contact' => 'required|in:email,phone,sms',
        'notes' => 'nullable|string|max:2000',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'client',
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth ?: null,
            'gender' => $this->gender ?: null,
            'is_active' => true,
        ]);

        // Create client profile
        Client::create([
            'user_id' => $user->id,
            'preferences' => $this->preferences,
            'allergies' => $this->allergies,
            'medical_conditions' => $this->medical_conditions,
            'preferred_contact' => $this->preferred_contact,
            'notes' => $this->notes,
            'visit_count' => 0,
            'total_spent' => 0,
            'loyalty_points' => 0,
        ]);

        session()->flash('success', 'Client created successfully!');
        
        return redirect()->route('admin.clients.index');
    }

    public function render()
    {
        return view('livewire.admin.clients.create')->layout('layouts.admin');
    }
}
