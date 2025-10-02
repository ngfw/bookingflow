<?php

namespace App\Livewire\Admin\Clients;

use Livewire\Component;
use App\Models\Client;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    public Client $client;
    
    // User fields
    public $name = '';
    public $email = '';
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
        'email' => 'required|string|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'allergies' => 'nullable|string|max:1000',
        'medical_conditions' => 'nullable|string|max:1000',
        'preferred_contact' => 'required|in:email,phone,sms',
        'notes' => 'nullable|string|max:2000',
    ];

    public function mount(Client $client)
    {
        $this->client = $client;
        
        // Load user data
        $this->name = $client->user->name;
        $this->email = $client->user->email;
        $this->phone = $client->user->phone;
        $this->address = $client->user->address;
        $this->date_of_birth = $client->user->date_of_birth?->format('Y-m-d');
        $this->gender = $client->user->gender;

        // Load client data
        $this->preferences = $client->preferences ?? [];
        $this->allergies = $client->allergies;
        $this->medical_conditions = $client->medical_conditions;
        $this->preferred_contact = $client->preferred_contact;
        $this->notes = $client->notes;

        // Update email validation rule
        $this->rules['email'] = 'required|string|email|max:255|unique:users,email,' . $client->user_id;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        // Update user
        $this->client->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth ?: null,
            'gender' => $this->gender ?: null,
        ]);

        // Update client profile
        $this->client->update([
            'preferences' => $this->preferences,
            'allergies' => $this->allergies,
            'medical_conditions' => $this->medical_conditions,
            'preferred_contact' => $this->preferred_contact,
            'notes' => $this->notes,
        ]);

        session()->flash('success', 'Client updated successfully!');
        
        return redirect()->route('admin.clients.index');
    }

    public function render()
    {
        $appointments = $this->client->appointments()
            ->with(['service', 'staff.user'])
            ->orderBy('appointment_date', 'desc')
            ->paginate(10);

        return view('livewire.admin.clients.edit', [
            'appointments' => $appointments,
        ])->layout('layouts.admin');
    }
}
