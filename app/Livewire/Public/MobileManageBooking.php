<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\User;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
class MobileManageBooking extends Component
{
    public $email = '';
    public $phone = '';
    public $appointments = [];
    public $selectedAppointment = null;
    public $showCancelModal = false;
    public $showRescheduleModal = false;
    public $newDate = '';
    public $newTime = '';
    public $availableTimeSlots = [];
    public $loading = false;

    public function mount()
    {
        // Auto-focus on email input for mobile
        $this->dispatch('focus-email');
    }

    public function searchAppointments()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $this->loading = true;
        
        $user = User::where('email', $this->email)->first();
        
        if ($user && $user->client) {
            $this->appointments = $user->client->appointments()
                ->with(['service', 'staff.user'])
                ->where('appointment_date', '>=', now())
                ->orderBy('appointment_date')
                ->get();
        } else {
            $this->appointments = [];
            session()->flash('error', 'No appointments found for this email address.');
        }
        
        $this->loading = false;
    }

    public function selectAppointment($appointmentId)
    {
        $this->selectedAppointment = $this->appointments->find($appointmentId);
    }

    public function showCancelConfirmation($appointmentId)
    {
        $this->selectedAppointment = $this->appointments->find($appointmentId);
        $this->showCancelModal = true;
    }

    public function cancelAppointment()
    {
        if ($this->selectedAppointment) {
            $this->selectedAppointment->update(['status' => 'cancelled']);
            $this->appointments = $this->appointments->reject(function ($appointment) {
                return $appointment->id === $this->selectedAppointment->id;
            });
            $this->showCancelModal = false;
            $this->selectedAppointment = null;
            session()->flash('success', 'Appointment cancelled successfully.');
        }
    }

    public function showRescheduleConfirmation($appointmentId)
    {
        $this->selectedAppointment = $this->appointments->find($appointmentId);
        $this->newDate = $this->selectedAppointment->appointment_date->format('Y-m-d');
        $this->loadAvailableTimeSlots();
        $this->showRescheduleModal = true;
    }

    public function loadAvailableTimeSlots()
    {
        if (!$this->newDate || !$this->selectedAppointment) {
            return;
        }

        $this->availableTimeSlots = [];
        $service = $this->selectedAppointment->service;
        $selectedDate = Carbon::parse($this->newDate);
        
        // Get all staff members who can perform this service
        $staffMembers = \App\Models\Staff::whereHas('services', function($query) use ($service) {
            $query->where('service_id', $service->id);
        })->get();

        foreach ($staffMembers as $staff) {
            // Get staff working hours for the selected date
            $schedule = \App\Models\Schedule::where('staff_id', $staff->id)
                ->where('day_of_week', $selectedDate->dayOfWeek)
                ->first();

            if (!$schedule || !$schedule->is_working) {
                continue;
            }

            $startTime = Carbon::parse($schedule->start_time);
            $endTime = Carbon::parse($schedule->end_time);
            $breakStart = $schedule->break_start ? Carbon::parse($schedule->break_start) : null;
            $breakEnd = $schedule->break_end ? Carbon::parse($schedule->break_end) : null;

            // Generate time slots
            $currentTime = $startTime->copy();
            while ($currentTime->addMinutes($service->duration_minutes)->lte($endTime)) {
                $slotTime = $currentTime->copy()->subMinutes($service->duration_minutes);
                
                // Skip break time
                if ($breakStart && $breakEnd && 
                    $slotTime->between($breakStart, $breakEnd)) {
                    $currentTime = $breakEnd->copy();
                    continue;
                }

                // Check if slot is available (exclude current appointment)
                $appointmentDateTime = $selectedDate->copy()->setTimeFromTimeString($slotTime->format('H:i:s'));
                
                $conflict = Appointment::where('staff_id', $staff->id)
                    ->where('appointment_date', $appointmentDateTime)
                    ->where('id', '!=', $this->selectedAppointment->id)
                    ->where('status', '!=', 'cancelled')
                    ->exists();

                if (!$conflict) {
                    $this->availableTimeSlots[] = [
                        'time' => $slotTime->format('H:i:s'),
                        'display' => $slotTime->format('g:i A'),
                        'staff_id' => $staff->id,
                        'staff_name' => $staff->user->name,
                        'available' => true,
                    ];
                }

                $currentTime = $slotTime->copy()->addMinutes(30); // 30-minute intervals
            }
        }

        // Sort by time
        usort($this->availableTimeSlots, function($a, $b) {
            return strcmp($a['time'], $b['time']);
        });
    }

    public function rescheduleAppointment()
    {
        if (!$this->selectedAppointment || !$this->newDate || !$this->newTime) {
            return;
        }

        $appointmentDateTime = Carbon::parse($this->newDate . ' ' . $this->newTime);
        
        $this->selectedAppointment->update([
            'appointment_date' => $appointmentDateTime,
            'status' => 'pending'
        ]);

        $this->showRescheduleModal = false;
        $this->selectedAppointment = null;
        $this->searchAppointments(); // Refresh the list
        session()->flash('success', 'Appointment rescheduled successfully.');
    }

    public function render()
    {
        return view('livewire.public.mobile-manage-booking');
    }
}

