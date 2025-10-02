<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use App\Models\Schedule;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class ManageBooking extends Component
{
    public $appointmentId = '';
    public $clientEmail = '';
    public $appointment = null;
    public $client = null;
    public $showModifyForm = false;
    public $modificationStep = 1; // 1: select new date/time, 2: confirm changes

    // Modification fields
    public $newDate = '';
    public $newTime = '';
    public $availableDates = [];
    public $availableTimeSlots = [];
    public $selectedStaff = '';

    public function mount()
    {
        // If appointment ID is provided in URL, try to load it
        if (request()->has('id')) {
            $this->appointmentId = request()->get('id');
            $this->loadAppointment();
        }
    }

    public function loadAppointment()
    {
        if (!$this->appointmentId || !$this->clientEmail) {
            return;
        }

        $this->appointment = Appointment::with(['client.user', 'staff.user', 'service'])
            ->where('id', $this->appointmentId)
            ->whereHas('client.user', function($query) {
                $query->where('email', $this->clientEmail);
            })
            ->first();

        if ($this->appointment) {
            $this->client = $this->appointment->client;
        }
    }

    public function updatedClientEmail()
    {
        $this->loadAppointment();
    }

    public function updatedAppointmentId()
    {
        $this->loadAppointment();
    }

    public function cancelAppointment()
    {
        if (!$this->appointment) {
            session()->flash('error', 'Appointment not found.');
            return;
        }

        // Check if appointment can be cancelled (not too close to appointment time)
        $appointmentDateTime = Carbon::parse($this->appointment->appointment_date);
        $hoursUntilAppointment = Carbon::now()->diffInHours($appointmentDateTime, false);

        if ($hoursUntilAppointment < 2) {
            session()->flash('error', 'Appointments can only be cancelled at least 2 hours in advance.');
            return;
        }

        $this->appointment->update(['status' => 'cancelled']);
        session()->flash('success', 'Your appointment has been cancelled successfully.');
        
        // Reload the appointment
        $this->loadAppointment();
    }

    public function startModification()
    {
        if (!$this->appointment) {
            session()->flash('error', 'Appointment not found.');
            return;
        }

        // Check if appointment can be modified (not too close to appointment time)
        $appointmentDateTime = Carbon::parse($this->appointment->appointment_date);
        $hoursUntilAppointment = Carbon::now()->diffInHours($appointmentDateTime, false);

        if ($hoursUntilAppointment < 2) {
            session()->flash('error', 'Appointments can only be modified at least 2 hours in advance.');
            return;
        }

        $this->showModifyForm = true;
        $this->modificationStep = 1;
        $this->loadAvailableDates();
    }

    public function loadAvailableDates()
    {
        if (!$this->appointment) {
            $this->availableDates = [];
            return;
        }

        $dates = [];
        $startDate = Carbon::today();
        
        // Load next 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            
            // Skip the current appointment date
            if ($date->format('Y-m-d') === Carbon::parse($this->appointment->appointment_date)->format('Y-m-d')) {
                continue;
            }
            
            // Check if any staff is available on this day
            $hasAvailableStaff = $this->checkStaffAvailability($date);
            
            if ($hasAvailableStaff) {
                $dates[] = [
                    'date' => $date->format('Y-m-d'),
                    'display' => $date->format('M j, Y'),
                    'day' => $date->format('l'),
                ];
            }
        }

        $this->availableDates = $dates;
    }

    public function checkStaffAvailability($date)
    {
        $service = $this->appointment->service;
        if (!$service) return false;

        $availableStaff = Staff::whereHas('services', function($query) use ($service) {
            $query->where('service_id', $service->id);
        })->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        foreach ($availableStaff as $staff) {
            $schedule = Schedule::where('staff_id', $staff->id)
                ->where('day_of_week', $date->dayOfWeek)
                ->where('is_active', true)
                ->first();

            if ($schedule) {
                return true;
            }
        }

        return false;
    }

    public function selectNewDate($date)
    {
        $this->newDate = $date;
        $this->loadAvailableTimeSlots();
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->appointment || !$this->newDate) {
            $this->availableTimeSlots = [];
            return;
        }

        $service = $this->appointment->service;
        $appointmentDate = Carbon::parse($this->newDate);

        // Get available staff for this service
        $availableStaff = Staff::whereHas('services', function($query) use ($service) {
            $query->where('service_id', $service->id);
        })->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        $allTimeSlots = [];

        foreach ($availableStaff as $staff) {
            $schedule = Schedule::where('staff_id', $staff->id)
                ->where('day_of_week', $appointmentDate->dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (!$schedule) continue;

            $timeSlots = $this->generateTimeSlotsForStaff($schedule, $service, $appointmentDate, $staff);
            $allTimeSlots = array_merge($allTimeSlots, $timeSlots);
        }

        // Sort time slots by time
        usort($allTimeSlots, function($a, $b) {
            return strcmp($a['time'], $b['time']);
        });

        $this->availableTimeSlots = $allTimeSlots;
    }

    public function generateTimeSlotsForStaff($schedule, $service, $appointmentDate, $staff)
    {
        $timeSlots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $serviceDuration = $service->duration_minutes;
        $bufferTime = $service->buffer_time_minutes ?? 0;
        $slotDuration = $serviceDuration + $bufferTime;

        // Get existing appointments for this staff on this date (excluding current appointment)
        $existingAppointments = Appointment::where('staff_id', $staff->id)
            ->whereDate('appointment_date', $appointmentDate)
            ->where('id', '!=', $this->appointment->id)
            ->where('status', '!=', 'cancelled')
            ->get();

        $currentTime = $startTime->copy();
        
        while ($currentTime->addMinutes($slotDuration)->lte($endTime)) {
            $slotStart = $currentTime->copy()->subMinutes($slotDuration);
            $slotEnd = $currentTime->copy();
            
            // Check if this time slot conflicts with existing appointments
            $hasConflict = $existingAppointments->some(function($appointment) use ($slotStart, $slotEnd) {
                $appointmentStart = Carbon::parse($appointment->appointment_date);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->service->duration_minutes);
                
                return $slotStart->lt($appointmentEnd) && $slotEnd->gt($appointmentStart);
            });

            if (!$hasConflict) {
                $timeSlots[] = [
                    'time' => $slotStart->format('H:i'),
                    'display' => $slotStart->format('g:i A'),
                    'end_time' => $slotEnd->format('g:i A'),
                    'staff_id' => $staff->id,
                    'staff_name' => $staff->user->name,
                ];
            }
        }

        return $timeSlots;
    }

    public function selectNewTimeSlot($timeSlot)
    {
        $this->newTime = $timeSlot['time'];
        $this->selectedStaff = $timeSlot['staff_id'];
    }

    public function confirmModification()
    {
        if (!$this->appointment || !$this->newDate || !$this->newTime) {
            session()->flash('error', 'Please select a new date and time.');
            return;
        }

        // Check if time slot is still available
        $appointmentDateTime = Carbon::parse($this->newDate . ' ' . $this->newTime);
        
        $conflict = Appointment::where('staff_id', $this->selectedStaff)
            ->where('appointment_date', $appointmentDateTime)
            ->where('id', '!=', $this->appointment->id)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($conflict) {
            session()->flash('error', 'This time slot is no longer available. Please select another time.');
            $this->loadAvailableTimeSlots();
            return;
        }

        // Update the appointment
        $this->appointment->update([
            'staff_id' => $this->selectedStaff,
            'appointment_date' => $appointmentDateTime,
        ]);

        session()->flash('success', 'Your appointment has been successfully rescheduled!');
        
        // Reset modification form
        $this->showModifyForm = false;
        $this->modificationStep = 1;
        $this->newDate = '';
        $this->newTime = '';
        $this->availableTimeSlots = [];
        $this->selectedStaff = '';
        
        // Reload the appointment
        $this->loadAppointment();
    }

    public function cancelModification()
    {
        $this->showModifyForm = false;
        $this->modificationStep = 1;
        $this->newDate = '';
        $this->newTime = '';
        $this->availableTimeSlots = [];
        $this->selectedStaff = '';
    }

    public function render()
    {
        return view('livewire.public.manage-booking');
    }
}
