<?php

namespace App\Livewire\Admin\Staff;

use Livewire\Component;
use App\Models\Staff;
use App\Models\Schedule;
use Carbon\Carbon;

class MobileStaffSchedule extends Component
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

    // Mobile-specific properties
    public $showStaffSelector = false;
    public $showDatePicker = false;
    public $quickDateOptions = [];
    public $viewMode = 'list'; // list, calendar, week

    protected $rules = [
        'formStaffId' => 'required|exists:staff,id',
        'formDate' => 'required|date',
        'formStartTime' => 'required',
        'formEndTime' => 'required|after:formStartTime',
        'formStatus' => 'required|in:available,scheduled,unavailable,sick,vacation',
    ];

    public function mount()
    {
        $this->selectedDate = Carbon::now()->format('Y-m-d');
        $this->loadStaff();
        $this->loadSchedules();
        $this->loadQuickDateOptions();
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

    public function loadQuickDateOptions()
    {
        $this->quickDateOptions = [
            [
                'label' => 'Today',
                'date' => Carbon::now()->format('Y-m-d'),
                'is_today' => true,
            ],
            [
                'label' => 'Tomorrow',
                'date' => Carbon::now()->addDay()->format('Y-m-d'),
                'is_tomorrow' => true,
            ],
            [
                'label' => 'This Week',
                'date' => Carbon::now()->startOfWeek()->format('Y-m-d'),
                'is_week' => true,
            ],
            [
                'label' => 'Next Week',
                'date' => Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d'),
                'is_next_week' => true,
            ],
        ];
    }

    public function updatedSelectedStaff()
    {
        $this->loadSchedules();
    }

    public function updatedSelectedDate()
    {
        $this->loadSchedules();
    }

    public function selectQuickDate($date)
    {
        $this->selectedDate = $date;
        $this->loadSchedules();
        $this->showDatePicker = false;
    }

    public function toggleStaffSelector()
    {
        $this->showStaffSelector = !$this->showStaffSelector;
    }

    public function toggleDatePicker()
    {
        $this->showDatePicker = !$this->showDatePicker;
    }

    public function addSchedule()
    {
        if (!$this->selectedStaff) {
            $this->showStaffSelector = true;
            return;
        }

        $this->formStaffId = $this->selectedStaff;
        $this->formDate = $this->selectedDate;
        $this->formStartTime = '09:00';
        $this->formEndTime = '17:00';
        $this->formBreakStart = '12:00';
        $this->formBreakEnd = '13:00';
        $this->formStatus = 'available';
        $this->formNotes = '';
        $this->formIsRecurring = false;
        $this->editingSchedule = null;
        $this->showScheduleModal = true;
    }

    public function editSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        if ($schedule) {
            $this->editingSchedule = $schedule;
            $this->formStaffId = $schedule->staff_id;
            $this->formDate = $schedule->date->format('Y-m-d');
            $this->formStartTime = $schedule->start_time;
            $this->formEndTime = $schedule->end_time;
            $this->formBreakStart = $schedule->break_start;
            $this->formBreakEnd = $schedule->break_end;
            $this->formStatus = $schedule->status;
            $this->formNotes = $schedule->notes;
            $this->formIsRecurring = false;
            $this->showScheduleModal = true;
        }
    }

    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        if ($schedule) {
            $schedule->delete();
            $this->loadSchedules();
            session()->flash('success', 'Schedule deleted successfully.');
        }
    }

    public function saveSchedule()
    {
        $this->validate();

        $scheduleData = [
            'staff_id' => $this->formStaffId,
            'date' => $this->formDate,
            'start_time' => $this->formStartTime,
            'end_time' => $this->formEndTime,
            'break_start' => $this->formBreakStart,
            'break_end' => $this->formBreakEnd,
            'status' => $this->formStatus,
            'notes' => $this->formNotes,
            'is_working' => $this->formStatus !== 'unavailable',
        ];

        if ($this->editingSchedule) {
            $this->editingSchedule->update($scheduleData);
            session()->flash('success', 'Schedule updated successfully.');
        } else {
            Schedule::create($scheduleData);
            session()->flash('success', 'Schedule created successfully.');
        }

        $this->closeModal();
        $this->loadSchedules();
    }

    public function closeModal()
    {
        $this->showScheduleModal = false;
        $this->editingSchedule = null;
        $this->reset(['formStaffId', 'formDate', 'formStartTime', 'formEndTime', 'formBreakStart', 'formBreakEnd', 'formStatus', 'formNotes', 'formIsRecurring']);
    }

    public function duplicateSchedule($scheduleId)
    {
        $schedule = Schedule::find($scheduleId);
        if ($schedule) {
            $this->formStaffId = $schedule->staff_id;
            $this->formDate = Carbon::now()->addDay()->format('Y-m-d');
            $this->formStartTime = $schedule->start_time;
            $this->formEndTime = $schedule->end_time;
            $this->formBreakStart = $schedule->break_start;
            $this->formBreakEnd = $schedule->break_end;
            $this->formStatus = $schedule->status;
            $this->formNotes = $schedule->notes;
            $this->formIsRecurring = false;
            $this->editingSchedule = null;
            $this->showScheduleModal = true;
        }
    }

    public function render()
    {
        return view('livewire.admin.staff.mobile-staff-schedule');
    }
}

