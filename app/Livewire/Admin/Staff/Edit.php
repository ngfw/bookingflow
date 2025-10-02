<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Staff;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class Edit extends Component
{
    use WithFileUploads;

    public Staff $staff;
    public $staffId;
    
    // User information
    public $name;
    public $email;
    public $phone;
    public $password;
    public $password_confirmation;
    public $is_active;
    
    // Staff information
    public $employee_id;
    public $position;
    public $specializations = [];
    public $hourly_rate;
    public $commission_rate;
    public $hire_date;
    public $employment_type = 'full_time';
    public $default_start_time;
    public $default_end_time;
    public $working_days = [];
    public $can_book_online = true;
    public $bio;
    public $profile_image;
    public $new_profile_image;
    
    // Experience and professional information
    public $experience_years;
    public $certifications;
    public $education;
    public $achievements;
    public $social_media = [];
    public $languages;
    public $hobbies;
    
    // Available options
    public $availableServices = [];
    public $selectedServices = [];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'phone' => 'nullable|string|max:20',
        'password' => 'nullable|min:8|confirmed',
        'employee_id' => 'nullable|string|max:50',
        'position' => 'required|string|max:255',
        'specializations' => 'nullable|array',
        'hourly_rate' => 'nullable|numeric|min:0',
        'commission_rate' => 'nullable|numeric|min:0|max:100',
        'hire_date' => 'required|date',
        'employment_type' => 'required|in:full_time,part_time,contract',
        'default_start_time' => 'nullable|date_format:H:i',
        'default_end_time' => 'nullable|date_format:H:i',
        'working_days' => 'nullable|array',
        'can_book_online' => 'boolean',
        'bio' => 'nullable|string',
        'new_profile_image' => 'nullable|image|max:2048',
        'experience_years' => 'nullable|integer|min:0|max:50',
        'certifications' => 'nullable|string',
        'education' => 'nullable|string',
        'achievements' => 'nullable|string',
        'social_media' => 'nullable|array',
        'languages' => 'nullable|string',
        'hobbies' => 'nullable|string',
    ];

    public function mount($staffId)
    {
        $this->staffId = $staffId;
        $this->staff = Staff::with(['user', 'services'])->findOrFail($staffId);
        
        // Load user data
        $this->name = $this->staff->user->name;
        $this->email = $this->staff->user->email;
        $this->phone = $this->staff->user->phone;
        $this->is_active = $this->staff->user->is_active;
        
        // Load staff data
        $this->employee_id = $this->staff->employee_id;
        $this->position = $this->staff->position;
        $this->specializations = $this->staff->specializations ?? [];
        $this->hourly_rate = $this->staff->hourly_rate;
        $this->commission_rate = $this->staff->commission_rate;
        $this->hire_date = $this->staff->hire_date?->format('Y-m-d');
        $this->employment_type = $this->staff->employment_type ?? 'full_time';
        $this->default_start_time = $this->staff->default_start_time?->format('H:i');
        $this->default_end_time = $this->staff->default_end_time?->format('H:i');
        $this->working_days = $this->staff->working_days ?? [];
        $this->can_book_online = $this->staff->can_book_online ?? true;
        $this->bio = $this->staff->bio;
        $this->profile_image = $this->staff->profile_image;
        
        // Load experience and professional information
        $this->experience_years = $this->staff->experience_years;
        $this->certifications = $this->staff->certifications;
        $this->education = $this->staff->education;
        $this->achievements = $this->staff->achievements;
        $this->social_media = $this->staff->social_media ?? [];
        $this->languages = $this->staff->languages;
        $this->hobbies = $this->staff->hobbies;
        
        // Load available services and selected services
        $this->availableServices = Service::all();
        $this->selectedServices = $this->staff->services->pluck('id')->toArray();
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->staff->user_id)],
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:8|confirmed',
            'employee_id' => 'nullable|string|max:50',
            'position' => 'required|string|max:255',
            'specializations' => 'nullable|array',
            'hourly_rate' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'hire_date' => 'required|date',
            'employment_type' => 'required|in:full_time,part_time,contract',
            'default_start_time' => 'nullable|date_format:H:i',
            'default_end_time' => 'nullable|date_format:H:i',
            'working_days' => 'nullable|array',
            'can_book_online' => 'boolean',
            'bio' => 'nullable|string',
            'new_profile_image' => 'nullable|image|max:2048',
        ];
    }

    public function updateStaff()
    {
        $this->validate();

        try {
            // Update user information
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'is_active' => $this->is_active,
            ];

            if ($this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            $this->staff->user->update($userData);

            // Handle profile image upload
            $profileImagePath = $this->profile_image;
            if ($this->new_profile_image) {
                // Delete old image if exists
                if ($this->profile_image) {
                    Storage::disk('public')->delete($this->profile_image);
                }
                
                $profileImagePath = $this->new_profile_image->store('staff-profiles', 'public');
            }

            // Update staff information
            $this->staff->update([
                'employee_id' => $this->employee_id,
                'position' => $this->position,
                'specializations' => $this->specializations,
                'hourly_rate' => $this->hourly_rate,
                'commission_rate' => $this->commission_rate,
                'hire_date' => $this->hire_date,
                'employment_type' => $this->employment_type,
                'default_start_time' => $this->default_start_time,
                'default_end_time' => $this->default_end_time,
                'working_days' => $this->working_days,
                'can_book_online' => $this->can_book_online,
                'bio' => $this->bio,
                'profile_image' => $profileImagePath,
                'experience_years' => $this->experience_years,
                'certifications' => $this->certifications,
                'education' => $this->education,
                'achievements' => $this->achievements,
                'social_media' => $this->social_media,
                'languages' => $this->languages,
                'hobbies' => $this->hobbies,
            ]);

            // Update services
            $this->staff->services()->sync($this->selectedServices);

            session()->flash('success', 'Staff member updated successfully.');
            
            return redirect()->route('admin.staff.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating staff member: ' . $e->getMessage());
        }
    }

    public function deleteProfileImage()
    {
        if ($this->profile_image) {
            Storage::disk('public')->delete($this->profile_image);
            $this->staff->update(['profile_image' => null]);
            $this->profile_image = null;
        }
    }

    public function render()
    {
        return view('livewire.admin.staff.edit')->layout('layouts.admin');
    }
}
