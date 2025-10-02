<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;

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
    
    // Staff specific fields
    public $specialization = '';
    public $experience_years = '';
    public $hourly_rate = '';
    public $commission_rate = '';
    public $skills = [];
    public $certifications = [];
    public $assigned_services = [];
    public $notes = '';
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'date_of_birth' => 'nullable|date',
        'gender' => 'nullable|in:male,female,other',
        'specialization' => 'required|string|max:255',
        'experience_years' => 'required|integer|min:0',
        'hourly_rate' => 'required|numeric|min:0',
        'commission_rate' => 'nullable|numeric|min:0|max:100',
        'notes' => 'nullable|string|max:2000',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => 'staff',
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth ?: null,
            'gender' => $this->gender ?: null,
            'is_active' => true,
        ]);

        $staff = Staff::create([
            'user_id' => $user->id,
            'specialization' => $this->specialization,
            'experience_years' => $this->experience_years,
            'hourly_rate' => $this->hourly_rate,
            'commission_rate' => $this->commission_rate,
            'skills' => $this->skills,
            'certifications' => $this->certifications,
            'notes' => $this->notes,
        ]);

        // Assign services to staff
        if (!empty($this->assigned_services)) {
            $staff->services()->sync($this->assigned_services);
        }

        session()->flash('success', 'Staff member created successfully!');
        return redirect()->route('admin.staff.index');
    }

    public function render()
    {
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.staff.create', [
            'services' => $services,
        ])->layout('layouts.admin');
    }
}
