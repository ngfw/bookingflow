<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use App\Models\User;
use App\Models\NotificationPreference;
use Carbon\Carbon;

class NotificationPreferencesManagement extends Component
{
    public $users = [];
    public $preferences = [];
    public $selectedUser = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingPreference = null;

    // Form properties
    public $formUserId = '';
    public $formNotificationType = '';
    public $formEmailEnabled = true;
    public $formSmsEnabled = false;
    public $formPushEnabled = true;
    public $formPhoneEnabled = false;
    public $formTimingPreferences = [];
    public $formFrequencyPreferences = [];
    public $formContentPreferences = [];
    public $formPreferredLanguage = 'en';
    public $formTimezone = 'UTC';
    public $formIsActive = true;

    // Statistics
    public $totalPreferences = 0;
    public $activePreferences = 0;
    public $emailEnabledCount = 0;
    public $smsEnabledCount = 0;
    public $pushEnabledCount = 0;

    public function mount()
    {
        $this->loadUsers();
        $this->loadPreferences();
        $this->calculateStatistics();
    }

    public function loadUsers()
    {
        $this->users = User::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadPreferences()
    {
        $query = NotificationPreference::with('user');
        
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }
        
        $this->preferences = $query->orderBy('user_id')
            ->orderBy('notification_type')
            ->get();
    }

    public function calculateStatistics()
    {
        $this->totalPreferences = NotificationPreference::count();
        $this->activePreferences = NotificationPreference::where('is_active', true)->count();
        $this->emailEnabledCount = NotificationPreference::where('email_enabled', true)->where('is_active', true)->count();
        $this->smsEnabledCount = NotificationPreference::where('sms_enabled', true)->where('is_active', true)->count();
        $this->pushEnabledCount = NotificationPreference::where('push_enabled', true)->where('is_active', true)->count();
    }

    public function updatedSelectedUser()
    {
        $this->loadPreferences();
        $this->calculateStatistics();
    }

    public function showCreatePreferenceModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function editPreference($preferenceId)
    {
        $this->editingPreference = NotificationPreference::findOrFail($preferenceId);
        
        $this->formUserId = $this->editingPreference->user_id;
        $this->formNotificationType = $this->editingPreference->notification_type;
        $this->formEmailEnabled = $this->editingPreference->email_enabled;
        $this->formSmsEnabled = $this->editingPreference->sms_enabled;
        $this->formPushEnabled = $this->editingPreference->push_enabled;
        $this->formPhoneEnabled = $this->editingPreference->phone_enabled;
        $this->formTimingPreferences = $this->editingPreference->timing_preferences ?? [];
        $this->formFrequencyPreferences = $this->editingPreference->frequency_preferences ?? [];
        $this->formContentPreferences = $this->editingPreference->content_preferences ?? [];
        $this->formPreferredLanguage = $this->editingPreference->preferred_language;
        $this->formTimezone = $this->editingPreference->timezone;
        $this->formIsActive = $this->editingPreference->is_active;
        
        $this->showEditModal = true;
    }

    public function createPreference()
    {
        $this->validate([
            'formUserId' => 'required|exists:users,id',
            'formNotificationType' => 'required|string|max:255',
            'formEmailEnabled' => 'boolean',
            'formSmsEnabled' => 'boolean',
            'formPushEnabled' => 'boolean',
            'formPhoneEnabled' => 'boolean',
            'formPreferredLanguage' => 'required|string|max:10',
            'formTimezone' => 'required|string|max:50',
            'formIsActive' => 'boolean',
        ]);

        try {
            NotificationPreference::create([
                'user_id' => $this->formUserId,
                'notification_type' => $this->formNotificationType,
                'email_enabled' => $this->formEmailEnabled,
                'sms_enabled' => $this->formSmsEnabled,
                'push_enabled' => $this->formPushEnabled,
                'phone_enabled' => $this->formPhoneEnabled,
                'timing_preferences' => $this->formTimingPreferences,
                'frequency_preferences' => $this->formFrequencyPreferences,
                'content_preferences' => $this->formContentPreferences,
                'preferred_language' => $this->formPreferredLanguage,
                'timezone' => $this->formTimezone,
                'is_active' => $this->formIsActive,
            ]);

            session()->flash('success', 'Notification preference created successfully.');
            $this->closeCreateModal();
            $this->loadPreferences();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating preference: ' . $e->getMessage());
        }
    }

    public function updatePreference()
    {
        $this->validate([
            'formUserId' => 'required|exists:users,id',
            'formNotificationType' => 'required|string|max:255',
            'formEmailEnabled' => 'boolean',
            'formSmsEnabled' => 'boolean',
            'formPushEnabled' => 'boolean',
            'formPhoneEnabled' => 'boolean',
            'formPreferredLanguage' => 'required|string|max:10',
            'formTimezone' => 'required|string|max:50',
            'formIsActive' => 'boolean',
        ]);

        try {
            $this->editingPreference->update([
                'user_id' => $this->formUserId,
                'notification_type' => $this->formNotificationType,
                'email_enabled' => $this->formEmailEnabled,
                'sms_enabled' => $this->formSmsEnabled,
                'push_enabled' => $this->formPushEnabled,
                'phone_enabled' => $this->formPhoneEnabled,
                'timing_preferences' => $this->formTimingPreferences,
                'frequency_preferences' => $this->formFrequencyPreferences,
                'content_preferences' => $this->formContentPreferences,
                'preferred_language' => $this->formPreferredLanguage,
                'timezone' => $this->formTimezone,
                'is_active' => $this->formIsActive,
            ]);

            session()->flash('success', 'Notification preference updated successfully.');
            $this->closeEditModal();
            $this->loadPreferences();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating preference: ' . $e->getMessage());
        }
    }

    public function deletePreference($preferenceId)
    {
        try {
            $preference = NotificationPreference::findOrFail($preferenceId);
            $preference->delete();
            
            session()->flash('success', 'Notification preference deleted successfully.');
            $this->loadPreferences();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting preference: ' . $e->getMessage());
        }
    }

    public function createDefaultPreferencesForUser($userId)
    {
        try {
            NotificationPreference::createDefaultPreferencesForUser($userId);
            session()->flash('success', 'Default preferences created for user.');
            $this->loadPreferences();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating default preferences: ' . $e->getMessage());
        }
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingPreference = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->formUserId = '';
        $this->formNotificationType = '';
        $this->formEmailEnabled = true;
        $this->formSmsEnabled = false;
        $this->formPushEnabled = true;
        $this->formPhoneEnabled = false;
        $this->formTimingPreferences = [];
        $this->formFrequencyPreferences = [];
        $this->formContentPreferences = [];
        $this->formPreferredLanguage = 'en';
        $this->formTimezone = 'UTC';
        $this->formIsActive = true;
    }

    public function render()
    {
        return view('livewire.admin.notifications.notification-preferences-management')->layout('layouts.admin');
    }
}