<?php

namespace App\Livewire\Admin\Services;

use Livewire\Component;
use App\Models\Service;
use App\Models\Category;
use App\Models\Staff;
use App\Models\Product;

class Create extends Component
{
    // Basic service information
    public $name = '';
    public $description = '';
    public $category_id = '';
    public $price = '';
    public $duration_minutes = '';
    public $buffer_time_minutes = 0;
    
    // Service settings
    public $requires_deposit = false;
    public $deposit_amount = '';
    public $is_package = false;
    public $package_services = [];
    public $online_booking_enabled = true;
    public $max_advance_booking_days = 30;
    public $is_active = true;
    
    // Service details
    public $preparation_instructions = '';
    public $aftercare_instructions = '';
    public $required_products = [];
    public $assigned_staff = [];
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'required|string|max:1000',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'duration_minutes' => 'required|integer|min:1',
        'buffer_time_minutes' => 'integer|min:0',
        'requires_deposit' => 'boolean',
        'deposit_amount' => 'nullable|numeric|min:0',
        'is_package' => 'boolean',
        'online_booking_enabled' => 'boolean',
        'max_advance_booking_days' => 'integer|min:1|max:365',
        'is_active' => 'boolean',
        'preparation_instructions' => 'nullable|string|max:2000',
        'aftercare_instructions' => 'nullable|string|max:2000',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        $service = Service::create([
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'duration_minutes' => $this->duration_minutes,
            'buffer_time_minutes' => $this->buffer_time_minutes,
            'requires_deposit' => $this->requires_deposit,
            'deposit_amount' => $this->requires_deposit ? $this->deposit_amount : null,
            'is_package' => $this->is_package,
            'package_services' => $this->is_package ? $this->package_services : null,
            'online_booking_enabled' => $this->online_booking_enabled,
            'max_advance_booking_days' => $this->max_advance_booking_days,
            'is_active' => $this->is_active,
            'preparation_instructions' => $this->preparation_instructions,
            'aftercare_instructions' => $this->aftercare_instructions,
            'required_products' => $this->required_products,
        ]);

        // Assign staff to service
        if (!empty($this->assigned_staff)) {
            $service->staff()->sync($this->assigned_staff);
        }

        session()->flash('success', 'Service created successfully!');
        
        return redirect()->route('admin.services.index');
    }

    public function render()
    {
        $categories = Category::orderBy('name')->get();
        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.services.create', [
            'categories' => $categories,
            'staff' => $staff,
            'products' => $products,
        ])->layout('layouts.admin');
    }
}
