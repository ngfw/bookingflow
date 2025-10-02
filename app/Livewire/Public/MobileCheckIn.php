<?php

namespace App\Livewire\Public;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Service;
use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

#[Layout('components.layouts.app')]
class MobileCheckIn extends Component
{
    public $checkInMethod = 'phone'; // phone, email, qr
    public $phoneNumber = '';
    public $email = '';
    public $qrCode = '';
    public $appointment = null;
    public $client = null;
    public $checkInSuccessful = false;
    public $showAppointmentDetails = false;
    public $checkInTime = null;
    public $waitTime = null;
    public $staffReady = false;
    public $estimatedWaitTime = 0;

    protected $rules = [
        'phoneNumber' => 'required_if:checkInMethod,phone|string|min:10',
        'email' => 'required_if:checkInMethod,email|email',
        'qrCode' => 'required_if:checkInMethod,qr|string',
    ];

    public function mount()
    {
        // Set default check-in method based on mobile device
        $this->checkInMethod = 'phone';
    }

    public function updatedCheckInMethod()
    {
        $this->reset(['phoneNumber', 'email', 'qrCode', 'appointment', 'client', 'checkInSuccessful']);
    }

    public function searchAppointment()
    {
        $this->validate();

        $query = Appointment::with(['client.user', 'service', 'staff.user'])
            ->where('appointment_date', '>=', Carbon::today())
            ->where('status', 'confirmed');

        if ($this->checkInMethod === 'phone') {
            $query->whereHas('client.user', function ($q) {
                $q->where('phone', 'like', '%' . $this->phoneNumber . '%');
            });
        } elseif ($this->checkInMethod === 'email') {
            $query->whereHas('client.user', function ($q) {
                $q->where('email', $this->email);
            });
        } elseif ($this->checkInMethod === 'qr') {
            $query->where('appointment_number', $this->qrCode);
        }

        $this->appointment = $query->first();

        if ($this->appointment) {
            $this->client = $this->appointment->client;
            $this->showAppointmentDetails = true;
            $this->calculateWaitTime();
        } else {
            session()->flash('error', 'No appointment found. Please check your information and try again.');
        }
    }

    public function checkIn()
    {
        if (!$this->appointment) {
            session()->flash('error', 'No appointment found to check in.');
            return;
        }

        // Check if appointment is within check-in window (15 minutes before)
        $appointmentTime = Carbon::parse($this->appointment->appointment_date);
        $checkInWindowStart = $appointmentTime->copy()->subMinutes(15);
        $checkInWindowEnd = $appointmentTime->copy()->addMinutes(30);

        if (now()->lt($checkInWindowStart)) {
            session()->flash('error', 'You can check in up to 15 minutes before your appointment. Your appointment is at ' . $appointmentTime->format('g:i A') . '.');
            return;
        }

        if (now()->gt($checkInWindowEnd)) {
            session()->flash('error', 'Check-in window has expired. Please contact staff for assistance.');
            return;
        }

        // Check if already checked in
        if ($this->appointment->status === 'in_progress') {
            session()->flash('error', 'You have already checked in for this appointment.');
            return;
        }

        try {
            $this->appointment->update([
                'status' => 'in_progress',
                'checked_in_at' => now(),
            ]);

            $this->checkInTime = now();
            $this->checkInSuccessful = true;
            $this->calculateWaitTime();

            // Send notification to staff
            $this->notifyStaff();

            session()->flash('success', 'Check-in successful! Your staff member has been notified.');

        } catch (\Exception $e) {
            session()->flash('error', 'Check-in failed. Please try again or contact staff.');
        }
    }

    public function calculateWaitTime()
    {
        if (!$this->appointment) {
            return;
        }

        $appointmentTime = Carbon::parse($this->appointment->appointment_date);
        $now = now();

        if ($now->lt($appointmentTime)) {
            $this->waitTime = $now->diffInMinutes($appointmentTime);
            $this->staffReady = false;
        } else {
            $this->waitTime = 0;
            $this->staffReady = true;
        }

        // Calculate estimated wait time based on current appointments
        $this->estimatedWaitTime = $this->calculateEstimatedWaitTime();
    }

    private function calculateEstimatedWaitTime()
    {
        if (!$this->appointment) {
            return 0;
        }

        // Get appointments for the same staff member that are in progress or completed today
        $staffAppointments = Appointment::where('staff_id', $this->appointment->staff_id)
            ->whereDate('appointment_date', Carbon::today())
            ->whereIn('status', ['in_progress', 'completed'])
            ->where('appointment_date', '<', $this->appointment->appointment_date)
            ->orderBy('appointment_date', 'desc')
            ->get();

        if ($staffAppointments->isEmpty()) {
            return 0;
        }

        // Calculate average service time
        $totalServiceTime = 0;
        $completedCount = 0;

        foreach ($staffAppointments as $apt) {
            if ($apt->status === 'completed' && $apt->service) {
                $totalServiceTime += $apt->service->duration_minutes;
                $completedCount++;
            }
        }

        if ($completedCount === 0) {
            return 0;
        }

        $averageServiceTime = $totalServiceTime / $completedCount;
        return round($averageServiceTime);
    }

    private function notifyStaff()
    {
        // In a real application, this would send a push notification or SMS to staff
        // For now, we'll just log it
        \Log::info('Client checked in', [
            'appointment_id' => $this->appointment->id,
            'client_name' => $this->client->user->name,
            'service' => $this->appointment->service->name,
            'check_in_time' => $this->checkInTime,
        ]);
    }

    public function rescheduleAppointment()
    {
        if (!$this->appointment) {
            return;
        }

        // Redirect to mobile booking with pre-filled data
        return redirect()->route('mobile-booking', [
            'service_id' => $this->appointment->service_id,
            'staff_id' => $this->appointment->staff_id,
            'reschedule' => $this->appointment->id,
        ]);
    }

    public function cancelAppointment()
    {
        if (!$this->appointment) {
            return;
        }

        try {
            $this->appointment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => 'Cancelled by client via mobile check-in',
            ]);

            session()->flash('success', 'Appointment cancelled successfully.');
            $this->reset(['appointment', 'client', 'showAppointmentDetails', 'checkInSuccessful']);

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to cancel appointment. Please contact staff.');
        }
    }

    public function newCheckIn()
    {
        $this->reset([
            'phoneNumber', 'email', 'qrCode', 'appointment', 'client', 
            'checkInSuccessful', 'showAppointmentDetails', 'checkInTime', 'waitTime'
        ]);
    }

    public function render()
    {
        return view('livewire.public.mobile-check-in');
    }
}

