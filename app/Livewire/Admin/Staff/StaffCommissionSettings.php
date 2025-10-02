<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\Service;
use App\Models\StaffCommissionSetting;
use Carbon\Carbon;

class StaffCommissionSettings extends Component
{
    public $staff = [];
    public $services = [];
    public $commissionSettings = [];
    public $selectedStaff = '';
    public $showModal = false;
    public $editingSetting = null;

    // Form properties
    public $formStaffId = '';
    public $formServiceId = '';
    public $formCommissionType = 'percentage';
    public $formCommissionRate = 0;
    public $formFixedAmount = 0;
    public $formTieredRates = [];
    public $formMinimumThreshold = 0;
    public $formMaximumCap = null;
    public $formCalculationBasis = 'revenue';
    public $formPaymentFrequency = 'monthly';
    public $formIsActive = true;
    public $formEffectiveDate = '';
    public $formExpiryDate = '';
    public $formNotes = '';

    // Tiered rates management
    public $showTieredRates = false;
    public $newTierMin = 0;
    public $newTierMax = 0;
    public $newTierRate = 0;

    public function mount()
    {
        $this->loadStaff();
        $this->loadServices();
        $this->loadCommissionSettings();
        $this->formEffectiveDate = Carbon::now()->format('Y-m-d');
    }

    public function loadStaff()
    {
        $this->staff = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();
    }

    public function loadServices()
    {
        $this->services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadCommissionSettings()
    {
        $query = StaffCommissionSetting::with(['staff.user', 'service']);
        
        if ($this->selectedStaff) {
            $query->where('staff_id', $this->selectedStaff);
        }
        
        $this->commissionSettings = $query->orderBy('staff_id')
            ->orderBy('service_id')
            ->get();
    }

    public function updatedSelectedStaff()
    {
        $this->loadCommissionSettings();
    }

    public function updatedFormCommissionType()
    {
        $this->showTieredRates = $this->formCommissionType === 'tiered';
        if (!$this->showTieredRates) {
            $this->formTieredRates = [];
        }
    }

    public function addCommissionSetting()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editCommissionSetting($settingId)
    {
        $setting = StaffCommissionSetting::findOrFail($settingId);
        $this->editingSetting = $setting;
        
        $this->formStaffId = $setting->staff_id;
        $this->formServiceId = $setting->service_id;
        $this->formCommissionType = $setting->commission_type;
        $this->formCommissionRate = $setting->commission_rate;
        $this->formFixedAmount = $setting->fixed_amount;
        $this->formTieredRates = $setting->tiered_rates ?? [];
        $this->formMinimumThreshold = $setting->minimum_threshold;
        $this->formMaximumCap = $setting->maximum_cap;
        $this->formCalculationBasis = $setting->calculation_basis;
        $this->formPaymentFrequency = $setting->payment_frequency;
        $this->formIsActive = $setting->is_active;
        $this->formEffectiveDate = $setting->effective_date->format('Y-m-d');
        $this->formExpiryDate = $setting->expiry_date ? $setting->expiry_date->format('Y-m-d') : '';
        $this->formNotes = $setting->notes;
        
        $this->showTieredRates = $this->formCommissionType === 'tiered';
        $this->showModal = true;
    }

    public function saveCommissionSetting()
    {
        $this->validate([
            'formStaffId' => 'required|exists:staff,id',
            'formServiceId' => 'nullable|exists:services,id',
            'formCommissionType' => 'required|in:percentage,fixed,tiered',
            'formCommissionRate' => 'required_if:formCommissionType,percentage|numeric|min:0|max:100',
            'formFixedAmount' => 'required_if:formCommissionType,fixed|numeric|min:0',
            'formTieredRates' => 'required_if:formCommissionType,tiered|array|min:1',
            'formMinimumThreshold' => 'numeric|min:0',
            'formMaximumCap' => 'nullable|numeric|min:0',
            'formCalculationBasis' => 'required|in:revenue,profit,appointments',
            'formPaymentFrequency' => 'required|in:daily,weekly,bi_weekly,monthly',
            'formIsActive' => 'boolean',
            'formEffectiveDate' => 'required|date',
            'formExpiryDate' => 'nullable|date|after:formEffectiveDate',
            'formNotes' => 'nullable|string|max:500',
        ]);

        $data = [
            'staff_id' => $this->formStaffId,
            'service_id' => $this->formServiceId ?: null,
            'commission_type' => $this->formCommissionType,
            'commission_rate' => $this->formCommissionRate,
            'fixed_amount' => $this->formCommissionType === 'fixed' ? $this->formFixedAmount : null,
            'tiered_rates' => $this->formCommissionType === 'tiered' ? $this->formTieredRates : null,
            'minimum_threshold' => $this->formMinimumThreshold,
            'maximum_cap' => $this->formMaximumCap,
            'calculation_basis' => $this->formCalculationBasis,
            'payment_frequency' => $this->formPaymentFrequency,
            'is_active' => $this->formIsActive,
            'effective_date' => $this->formEffectiveDate,
            'expiry_date' => $this->formExpiryDate ?: null,
            'notes' => $this->formNotes,
        ];

        if ($this->editingSetting) {
            $this->editingSetting->update($data);
            session()->flash('success', 'Commission setting updated successfully.');
        } else {
            StaffCommissionSetting::create($data);
            session()->flash('success', 'Commission setting created successfully.');
        }

        $this->closeModal();
        $this->loadCommissionSettings();
    }

    public function deleteCommissionSetting($settingId)
    {
        $setting = StaffCommissionSetting::findOrFail($settingId);
        $setting->delete();
        
        session()->flash('success', 'Commission setting deleted successfully.');
        $this->loadCommissionSettings();
    }

    public function addTieredRate()
    {
        $this->validate([
            'newTierMin' => 'required|numeric|min:0',
            'newTierMax' => 'required|numeric|min:0|gt:newTierMin',
            'newTierRate' => 'required|numeric|min:0|max:100',
        ]);

        $this->formTieredRates[] = [
            'min' => $this->newTierMin,
            'max' => $this->newTierMax,
            'rate' => $this->newTierRate,
        ];

        $this->newTierMin = 0;
        $this->newTierMax = 0;
        $this->newTierRate = 0;
    }

    public function removeTieredRate($index)
    {
        unset($this->formTieredRates[$index]);
        $this->formTieredRates = array_values($this->formTieredRates);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingSetting = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->formStaffId = '';
        $this->formServiceId = '';
        $this->formCommissionType = 'percentage';
        $this->formCommissionRate = 0;
        $this->formFixedAmount = 0;
        $this->formTieredRates = [];
        $this->formMinimumThreshold = 0;
        $this->formMaximumCap = null;
        $this->formCalculationBasis = 'revenue';
        $this->formPaymentFrequency = 'monthly';
        $this->formIsActive = true;
        $this->formEffectiveDate = Carbon::now()->format('Y-m-d');
        $this->formExpiryDate = '';
        $this->formNotes = '';
        $this->showTieredRates = false;
        $this->newTierMin = 0;
        $this->newTierMax = 0;
        $this->newTierRate = 0;
    }

    public function render()
    {
        return view('livewire.admin.staff.staff-commission-settings')->layout('layouts.admin');
    }
}