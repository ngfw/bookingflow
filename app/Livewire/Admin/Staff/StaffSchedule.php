<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\Schedule;
use Carbon\Carbon;

class StaffSchedule extends Component
{
    public $selectedStaff = '';
    public $selectedDate = '';
    public $schedules = [];
    public $staff = [];
    
    // Schedule form properties
    public $showScheduleModal = false;
    public $editingSchedule = null;
    public $formStaffId = '';
    public $formDate = '';
    public $formStartTime = '';
    public $formEndTime = '';
    public $formBreakStart = '';
    public $formBreakEnd = '';
    public $formStatus = 'available';
    public $formNotes = '';
    public $formIsRecurring = false;
    public $formRecurringType = 'weekly';
    public $formRecurringEndDate = '';

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->loadStaff();
        $this->loadSchedules();
    }

    public function loadStaff()
    {
        $this->staff = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->get();
    }

    public function loadSchedules()
    {
        if ($this->selectedStaff) {
            $this->schedules = Schedule::where('staff_id', $this->selectedStaff)
                ->whereDate('date', $this->selectedDate)
                ->orderBy('start_time')
                ->get();
        } else {
            $this->schedules = Schedule::whereDate('date', $this->selectedDate)
                ->with('staff.user')
                ->orderBy('start_time')
                ->get();
        }
    }

    public function updatedSelectedStaff()
    {
        $this->loadSchedules();
    }

    public function updatedSelectedDate()
    {
        $this->loadSchedules();
    }

    public function addSchedule()
    {
        if (!$this->selectedStaff) {
            session()->flash('error', 'Please select a staff member first.');
            return;
        }

        $this->resetForm();
        $this->formStaffId = $this->selectedStaff;
        $this->formDate = $this->selectedDate;
        $this->formStartTime = '09:00';
        $this->formEndTime = '17:00';
        $this->formStatus = 'available';
        $this->showScheduleModal = true;
    }

    public function editSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $this->editingSchedule = $schedule;
        
        $this->formStaffId = $schedule->staff_id;
        $this->formDate = $schedule->date->format('Y-m-d');
        $this->formStartTime = $schedule->start_time->format('H:i');
        $this->formEndTime = $schedule->end_time->format('H:i');
        $this->formBreakStart = $schedule->break_start ? $schedule->break_start->format('H:i') : '';
        $this->formBreakEnd = $schedule->break_end ? $schedule->break_end->format('H:i') : '';
        $this->formStatus = $schedule->status;
        $this->formNotes = $schedule->notes;
        $this->formIsRecurring = $schedule->is_recurring;
        $this->formRecurringType = $schedule->recurring_type;
        $this->formRecurringEndDate = $schedule->recurring_end_date ? $schedule->recurring_end_date->format('Y-m-d') : '';
        
        $this->showScheduleModal = true;
    }

    public function saveSchedule()
    {
        $this->validate([
            'formStaffId' => 'required|exists:staff,id',
            'formDate' => 'required|date',
            'formStartTime' => 'required',
            'formEndTime' => 'required|after:formStartTime',
            'formBreakStart' => 'nullable',
            'formBreakEnd' => 'nullable|after:formBreakStart',
            'formStatus' => 'required|in:available,scheduled,unavailable,sick,vacation',
            'formNotes' => 'nullable|string|max:500',
            'formIsRecurring' => 'boolean',
            'formRecurringType' => 'required_if:formIsRecurring,true|in:daily,weekly,monthly',
            'formRecurringEndDate' => 'required_if:formIsRecurring,true|date|after:formDate',
        ]);

        $data = [
            'staff_id' => $this->formStaffId,
            'date' => $this->formDate,
            'start_time' => $this->formStartTime,
            'end_time' => $this->formEndTime,
            'break_start' => $this->formBreakStart ?: null,
            'break_end' => $this->formBreakEnd ?: null,
            'status' => $this->formStatus,
            'notes' => $this->formNotes,
            'is_recurring' => $this->formIsRecurring,
            'recurring_type' => $this->formIsRecurring ? $this->formRecurringType : null,
            'recurring_end_date' => $this->formIsRecurring ? $this->formRecurringEndDate : null,
        ];

        if ($this->editingSchedule) {
            $this->editingSchedule->update($data);
            session()->flash('success', 'Schedule updated successfully.');
        } else {
            Schedule::create($data);
            session()->flash('success', 'Schedule created successfully.');
        }

        $this->closeModal();
        $this->loadSchedules();
    }

    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->delete();
        
        session()->flash('success', 'Schedule deleted successfully.');
        $this->loadSchedules();
    }

    public function closeModal()
    {
        $this->showScheduleModal = false;
        $this->editingSchedule = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->formStaffId = '';
        $this->formDate = '';
        $this->formStartTime = '';
        $this->formEndTime = '';
        $this->formBreakStart = '';
        $this->formBreakEnd = '';
        $this->formStatus = 'available';
        $this->formNotes = '';
        $this->formIsRecurring = false;
        $this->formRecurringType = 'weekly';
        $this->formRecurringEndDate = '';
    }

    public function render()
    {
        return view('livewire.admin.staff.staff-schedule')->layout('layouts.admin');
    }
}
