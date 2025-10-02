<?php

namespace App\Livewire\Admin\Communication;

use Livewire\Component;
use App\Models\Client;
use App\Models\Staff;
use App\Models\ClientCommunicationHistory;
use Carbon\Carbon;

class ClientCommunicationHistoryManagement extends Component
{
    public $clients = [];
    public $staff = [];
    public $communications = [];
    public $selectedClient = '';
    public $selectedStaff = '';
    public $selectedType = '';
    public $selectedStatus = '';
    public $selectedDirection = '';
    public $startDate = '';
    public $endDate = '';
    public $showCreateModal = false;
    public $showDetailModal = false;
    public $selectedCommunication = null;

    // Form properties
    public $formClientId = '';
    public $formStaffId = '';
    public $formCommunicationType = 'email';
    public $formDirection = 'outbound';
    public $formSubject = '';
    public $formMessage = '';
    public $formChannel = '';
    public $formRecipient = '';
    public $formSender = '';
    public $formNotes = '';
    public $formIsImportant = false;
    public $formRequiresFollowUp = false;
    public $formFollowUpDate = '';
    public $formFollowUpNotes = '';

    // Statistics
    public $totalCommunications = 0;
    public $emailCount = 0;
    public $smsCount = 0;
    public $phoneCount = 0;
    public $inPersonCount = 0;
    public $pushCount = 0;
    public $importantCount = 0;
    public $followUpCount = 0;

    public function mount()
    {
        $this->loadClients();
        $this->loadStaff();
        $this->loadCommunications();
        $this->calculateStatistics();
        
        // Set default date range to last 30 days
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function loadClients()
    {
        $this->clients = Client::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get();
    }

    public function loadStaff()
    {
        $this->staff = Staff::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_active', true);
            })
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('staff.*')
            ->get();
    }

    public function loadCommunications()
    {
        $query = ClientCommunicationHistory::with(['client.user', 'staff.user', 'appointment']);
        
        if ($this->selectedClient) {
            $query->where('client_id', $this->selectedClient);
        }
        
        if ($this->selectedStaff) {
            $query->where('staff_id', $this->selectedStaff);
        }
        
        if ($this->selectedType) {
            $query->where('communication_type', $this->selectedType);
        }
        
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        if ($this->selectedDirection) {
            $query->where('direction', $this->selectedDirection);
        }
        
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate . ' 23:59:59');
        }
        
        $this->communications = $query->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    public function calculateStatistics()
    {
        $stats = ClientCommunicationHistory::getCommunicationStatistics($this->startDate, $this->endDate);
        
        $this->totalCommunications = $stats['total'];
        $this->emailCount = $stats['email'];
        $this->smsCount = $stats['sms'];
        $this->phoneCount = $stats['phone'];
        $this->inPersonCount = $stats['in_person'];
        $this->pushCount = $stats['push_notification'];
        $this->importantCount = $stats['important'];
        $this->followUpCount = $stats['follow_up_required'];
    }

    public function updatedSelectedClient()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function updatedSelectedStaff()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function updatedSelectedType()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function updatedSelectedStatus()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function updatedSelectedDirection()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function updatedStartDate()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function updatedEndDate()
    {
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function showCreateCommunicationModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function viewCommunication($communicationId)
    {
        $this->selectedCommunication = ClientCommunicationHistory::with(['client.user', 'staff.user', 'appointment'])
            ->findOrFail($communicationId);
        $this->showDetailModal = true;
    }

    public function createCommunication()
    {
        $this->validate([
            'formClientId' => 'required|exists:clients,id',
            'formStaffId' => 'nullable|exists:staff,id',
            'formCommunicationType' => 'required|in:email,sms,phone,in_person,push_notification,system_generated',
            'formDirection' => 'required|in:inbound,outbound',
            'formSubject' => 'nullable|string|max:255',
            'formMessage' => 'required|string|max:2000',
            'formChannel' => 'nullable|string|max:100',
            'formRecipient' => 'nullable|string|max:255',
            'formSender' => 'nullable|string|max:255',
            'formNotes' => 'nullable|string|max:500',
            'formIsImportant' => 'boolean',
            'formRequiresFollowUp' => 'boolean',
            'formFollowUpDate' => 'nullable|date|after:now',
            'formFollowUpNotes' => 'nullable|string|max:500',
        ]);

        try {
            $communication = ClientCommunicationHistory::create([
                'client_id' => $this->formClientId,
                'staff_id' => $this->formStaffId ?: null,
                'communication_type' => $this->formCommunicationType,
                'direction' => $this->formDirection,
                'subject' => $this->formSubject,
                'message' => $this->formMessage,
                'status' => 'sent',
                'channel' => $this->formChannel,
                'recipient' => $this->formRecipient,
                'sender' => $this->formSender,
                'sent_at' => now(),
                'notes' => $this->formNotes,
                'is_important' => $this->formIsImportant,
                'requires_follow_up' => $this->formRequiresFollowUp,
                'follow_up_date' => $this->formFollowUpDate ?: null,
                'follow_up_notes' => $this->formFollowUpNotes,
            ]);

            session()->flash('success', 'Communication logged successfully.');
            $this->closeCreateModal();
            $this->loadCommunications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error logging communication: ' . $e->getMessage());
        }
    }

    public function markAsImportant($communicationId)
    {
        $communication = ClientCommunicationHistory::findOrFail($communicationId);
        $communication->markAsImportant();
        
        session()->flash('success', 'Communication marked as important.');
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function markAsUnimportant($communicationId)
    {
        $communication = ClientCommunicationHistory::findOrFail($communicationId);
        $communication->markAsUnimportant();
        
        session()->flash('success', 'Communication marked as unimportant.');
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function setFollowUp($communicationId)
    {
        $communication = ClientCommunicationHistory::findOrFail($communicationId);
        $followUpDate = now()->addDays(7); // Default to 7 days from now
        $communication->setFollowUp($followUpDate, 'Follow up required');
        
        session()->flash('success', 'Follow-up set for this communication.');
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function clearFollowUp($communicationId)
    {
        $communication = ClientCommunicationHistory::findOrFail($communicationId);
        $communication->clearFollowUp();
        
        session()->flash('success', 'Follow-up cleared for this communication.');
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function deleteCommunication($communicationId)
    {
        $communication = ClientCommunicationHistory::findOrFail($communicationId);
        $communication->delete();
        
        session()->flash('success', 'Communication deleted successfully.');
        $this->loadCommunications();
        $this->calculateStatistics();
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedCommunication = null;
    }

    public function resetForm()
    {
        $this->formClientId = '';
        $this->formStaffId = '';
        $this->formCommunicationType = 'email';
        $this->formDirection = 'outbound';
        $this->formSubject = '';
        $this->formMessage = '';
        $this->formChannel = '';
        $this->formRecipient = '';
        $this->formSender = '';
        $this->formNotes = '';
        $this->formIsImportant = false;
        $this->formRequiresFollowUp = false;
        $this->formFollowUpDate = '';
        $this->formFollowUpNotes = '';
    }

    public function render()
    {
        return view('livewire.admin.communication.client-communication-history-management')->layout('layouts.admin');
    }
}