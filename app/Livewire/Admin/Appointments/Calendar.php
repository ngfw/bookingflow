<?php

namespace App\Livewire\Admin\Appointments;

use Livewire\Component;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Staff;
use App\Models\Service;
use Carbon\Carbon;

class Calendar extends Component
{
    public $currentDate;
    public $selectedDate;
    public $viewMode = 'month'; // month, week, day
    public $appointments = [];
    public $staffFilter = '';
    public $serviceFilter = '';

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->selectedDate = Carbon::now();
        $this->loadAppointments();
    }

    public function loadAppointments()
    {
        $startDate = $this->getViewStartDate();
        $endDate = $this->getViewEndDate();

        $query = Appointment::with(['client.user', 'staff.user', 'service'])
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->when($this->staffFilter, function ($query) {
                $query->where('staff_id', $this->staffFilter);
            })
            ->when($this->serviceFilter, function ($query) {
                $query->where('service_id', $this->serviceFilter);
            })
            ->orderBy('appointment_date');

        $this->appointments = $query->get();
    }

    public function getViewStartDate()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->currentDate->copy()->startOfWeek();
            case 'day':
                return $this->currentDate->copy()->startOfDay();
            default: // month
                return $this->currentDate->copy()->startOfMonth();
        }
    }

    public function getViewEndDate()
    {
        switch ($this->viewMode) {
            case 'week':
                return $this->currentDate->copy()->endOfWeek();
            case 'day':
                return $this->currentDate->copy()->endOfDay();
            default: // month
                return $this->currentDate->copy()->endOfMonth();
        }
    }

    public function previousPeriod()
    {
        switch ($this->viewMode) {
            case 'week':
                $this->currentDate->subWeek();
                break;
            case 'day':
                $this->currentDate->subDay();
                break;
            default: // month
                $this->currentDate->subMonth();
                break;
        }
        $this->loadAppointments();
    }

    public function nextPeriod()
    {
        switch ($this->viewMode) {
            case 'week':
                $this->currentDate->addWeek();
                break;
            case 'day':
                $this->currentDate->addDay();
                break;
            default: // month
                $this->currentDate->addMonth();
                break;
        }
        $this->loadAppointments();
    }

    public function goToToday()
    {
        $this->currentDate = Carbon::now();
        $this->loadAppointments();
    }

    public function selectDate($date)
    {
        $this->selectedDate = Carbon::parse($date);
        $this->loadAppointments();
    }

    public function changeViewMode($mode)
    {
        $this->viewMode = $mode;
        $this->loadAppointments();
    }

    public function updatedStaffFilter()
    {
        $this->loadAppointments();
    }

    public function updatedServiceFilter()
    {
        $this->loadAppointments();
    }

    public function getAppointmentsForDate($date)
    {
        return $this->appointments->filter(function ($appointment) use ($date) {
            return Carbon::parse($appointment->appointment_date)->format('Y-m-d') === $date;
        });
    }

    public function getCalendarDays()
    {
        $days = [];
        $startDate = $this->getViewStartDate();
        $endDate = $this->getViewEndDate();

        if ($this->viewMode === 'month') {
            // Get calendar grid for month view
            $firstDayOfMonth = $startDate->copy()->startOfMonth();
            $lastDayOfMonth = $startDate->copy()->endOfMonth();
            
            // Start from Sunday of the first week
            $calendarStart = $firstDayOfMonth->copy()->startOfWeek();
            $calendarEnd = $lastDayOfMonth->copy()->endOfWeek();

            $current = $calendarStart->copy();
            while ($current->lte($calendarEnd)) {
                $days[] = [
                    'date' => $current->format('Y-m-d'),
                    'day' => $current->day,
                    'isCurrentMonth' => $current->month === $startDate->month,
                    'isToday' => $current->isToday(),
                    'isSelected' => $current->format('Y-m-d') === $this->selectedDate->format('Y-m-d'),
                ];
                $current->addDay();
            }
        } else {
            // For week and day views, just show the period
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $days[] = [
                    'date' => $current->format('Y-m-d'),
                    'day' => $current->day,
                    'isCurrentMonth' => true,
                    'isToday' => $current->isToday(),
                    'isSelected' => $current->format('Y-m-d') === $this->selectedDate->format('Y-m-d'),
                ];
                $current->addDay();
            }
        }

        return $days;
    }

    public function render()
    {
        $staff = Staff::with('user')->whereHas('user', function($query) {
            $query->where('is_active', true);
        })->get();

        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.appointments.calendar', [
            'staff' => $staff,
            'services' => $services,
            'calendarDays' => $this->getCalendarDays(),
        ])->layout('layouts.admin');
    }
}
