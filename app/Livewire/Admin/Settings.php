<?php

namespace App\Livewire\Admin;

use App\Models\SalonSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Settings extends Component
{
    public $settings;
    public $booking_settings = [];
    
    // Form properties
    public $salon_name;
    public $salon_description;
    public $primary_color;
    public $secondary_color;
    public $accent_color;
    public $max_booking_days;
    public $min_booking_hours;
    public $allow_same_day_booking;
    public $booking_time_slots;
    public $enable_waitlist;
    public $require_payment_upfront;
    public $cancellation_deadline_hours;

    protected $rules = [
        'salon_name' => 'required|string|max:255',
        'salon_description' => 'nullable|string',
        'primary_color' => 'required|string|size:7',
        'secondary_color' => 'required|string|size:7',
        'accent_color' => 'required|string|size:7',
        'max_booking_days' => 'required|integer|min:1|max:365',
        'min_booking_hours' => 'required|integer|min:0|max:168',
        'allow_same_day_booking' => 'boolean',
        'booking_time_slots' => 'required|integer|in:15,30,60',
        'enable_waitlist' => 'boolean',
        'require_payment_upfront' => 'boolean',
        'cancellation_deadline_hours' => 'required|integer|min:0|max:168',
    ];

    public function mount()
    {
        $this->settings = SalonSetting::getDefault();
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->salon_name = $this->settings->salon_name;
        $this->salon_description = $this->settings->salon_description;
        $this->primary_color = $this->settings->primary_color;
        $this->secondary_color = $this->settings->secondary_color;
        $this->accent_color = $this->settings->accent_color;

        $bookingSettings = $this->settings->getBookingSettings();
        $this->max_booking_days = $bookingSettings['max_booking_days'];
        $this->min_booking_hours = $bookingSettings['min_booking_hours'];
        $this->allow_same_day_booking = $bookingSettings['allow_same_day_booking'];
        $this->booking_time_slots = $bookingSettings['booking_time_slots'];
        $this->enable_waitlist = $bookingSettings['enable_waitlist'];
        $this->require_payment_upfront = $bookingSettings['require_payment_upfront'];
        $this->cancellation_deadline_hours = $bookingSettings['cancellation_deadline_hours'];
    }

    public function saveSettings()
    {
        $this->validate();

        $this->settings->update([
            'salon_name' => $this->salon_name,
            'salon_description' => $this->salon_description,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'accent_color' => $this->accent_color,
            'booking_settings' => [
                'max_booking_days' => $this->max_booking_days,
                'min_booking_hours' => $this->min_booking_hours,
                'allow_same_day_booking' => $this->allow_same_day_booking,
                'booking_time_slots' => $this->booking_time_slots,
                'enable_waitlist' => $this->enable_waitlist,
                'require_payment_upfront' => $this->require_payment_upfront,
                'cancellation_deadline_hours' => $this->cancellation_deadline_hours,
            ],
        ]);

        session()->flash('message', 'Settings updated successfully!');
    }

    public function getBookingDaysOptions()
    {
        return SalonSetting::getBookingDaysOptions();
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'bookingDaysOptions' => $this->getBookingDaysOptions()
        ]);
    }
}