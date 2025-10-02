<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use App\Models\PushNotification;
use App\Models\User;
use App\Models\Promotion;
use App\Services\PushNotificationService;
use Carbon\Carbon;

class PushNotificationManagement extends Component
{
    public $notifications = [];
    public $users = [];
    public $promotions = [];
    public $selectedUser = '';
    public $selectedType = '';
    public $selectedStatus = '';
    public $startDate = '';
    public $endDate = '';
    public $showCreateModal = false;
    public $showPromotionModal = false;

    // Form properties
    public $formTitle = '';
    public $formMessage = '';
    public $formType = 'info';
    public $formUserId = '';
    public $formActionUrl = '';
    public $formActionText = '';
    public $formScheduledAt = '';
    public $formData = '';

    // Promotion form properties
    public $formPromotionId = '';
    public $formPromotionUserIds = [];

    // Statistics
    public $totalNotifications = 0;
    public $pendingNotifications = 0;
    public $sentNotifications = 0;
    public $failedNotifications = 0;

    protected $pushNotificationService;

    public function mount()
    {
        $this->pushNotificationService = app(PushNotificationService::class);
        $this->loadUsers();
        $this->loadPromotions();
        $this->loadNotifications();
        $this->calculateStatistics();
        
        // Set default date range to last 30 days
        $this->startDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->endDate = Carbon::now()->format('Y-m-d');
    }

    public function loadUsers()
    {
        $this->users = User::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadPromotions()
    {
        $this->promotions = Promotion::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function loadNotifications()
    {
        $query = PushNotification::with('user');
        
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }
        
        if ($this->selectedType) {
            $query->where('type', $this->selectedType);
        }
        
        if ($this->selectedStatus) {
            $query->where('status', $this->selectedStatus);
        }
        
        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate . ' 23:59:59');
        }
        
        $this->notifications = $query->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();
    }

    public function calculateStatistics()
    {
        $this->totalNotifications = PushNotification::count();
        $this->pendingNotifications = PushNotification::where('status', 'pending')->count();
        $this->sentNotifications = PushNotification::whereIn('status', ['sent', 'delivered', 'read'])->count();
        $this->failedNotifications = PushNotification::where('status', 'failed')->count();
    }

    public function updatedSelectedUser()
    {
        $this->loadNotifications();
    }

    public function updatedSelectedType()
    {
        $this->loadNotifications();
    }

    public function updatedSelectedStatus()
    {
        $this->loadNotifications();
    }

    public function updatedStartDate()
    {
        $this->loadNotifications();
    }

    public function updatedEndDate()
    {
        $this->loadNotifications();
    }

    public function showCreateNotificationModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function showPromotionNotificationModal()
    {
        $this->resetPromotionForm();
        $this->showPromotionModal = true;
    }

    public function createNotification()
    {
        $this->validate([
            'formTitle' => 'required|string|max:255',
            'formMessage' => 'required|string|max:1000',
            'formType' => 'required|in:info,success,warning,error,appointment,promotion',
            'formUserId' => 'required|exists:users,id',
            'formActionUrl' => 'nullable|url',
            'formActionText' => 'nullable|string|max:50',
            'formScheduledAt' => 'nullable|date|after:now',
            'formData' => 'nullable|json',
        ]);

        try {
            $data = null;
            if ($this->formData) {
                $data = json_decode($this->formData, true);
            }

            $notification = PushNotification::createNotification(
                $this->formTitle,
                $this->formMessage,
                [
                    'user_id' => $this->formUserId,
                    'type' => $this->formType,
                    'data' => $data,
                    'action_url' => $this->formActionUrl,
                    'action_text' => $this->formActionText,
                    'scheduled_at' => $this->formScheduledAt,
                ]
            );

            session()->flash('success', 'Push notification created successfully.');
            $this->closeCreateModal();
            $this->loadNotifications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating notification: ' . $e->getMessage());
        }
    }

    public function createPromotionNotification()
    {
        $this->validate([
            'formPromotionId' => 'required|exists:promotions,id',
            'formPromotionUserIds' => 'required|array|min:1',
            'formPromotionUserIds.*' => 'exists:users,id',
        ]);

        try {
            $promotion = Promotion::findOrFail($this->formPromotionId);
            $notifications = $this->pushNotificationService->sendPromotionNotification(
                $promotion,
                $this->formPromotionUserIds
            );

            session()->flash('success', "Promotion notification sent to " . count($notifications) . " users.");
            $this->closePromotionModal();
            $this->loadNotifications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending promotion notification: ' . $e->getMessage());
        }
    }

    public function sendNotification($notificationId)
    {
        try {
            $notification = PushNotification::findOrFail($notificationId);
            $success = $this->pushNotificationService->sendNotification($notification);
            
            if ($success) {
                session()->flash('success', 'Notification sent successfully.');
            } else {
                session()->flash('error', 'Failed to send notification.');
            }
            
            $this->loadNotifications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending notification: ' . $e->getMessage());
        }
    }

    public function deleteNotification($notificationId)
    {
        try {
            $notification = PushNotification::findOrFail($notificationId);
            $notification->delete();
            
            session()->flash('success', 'Notification deleted successfully.');
            $this->loadNotifications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting notification: ' . $e->getMessage());
        }
    }

    public function processPendingNotifications()
    {
        try {
            $processedCount = $this->pushNotificationService->processPendingNotifications();
            session()->flash('success', "Processed {$processedCount} pending notifications.");
            $this->loadNotifications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing notifications: ' . $e->getMessage());
        }
    }

    public function retryFailedNotifications()
    {
        try {
            $retryCount = $this->pushNotificationService->retryFailedNotifications();
            session()->flash('success', "Retried {$retryCount} failed notifications.");
            $this->loadNotifications();
            $this->calculateStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'Error retrying notifications: ' . $e->getMessage());
        }
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function closePromotionModal()
    {
        $this->showPromotionModal = false;
        $this->resetPromotionForm();
    }

    public function resetForm()
    {
        $this->formTitle = '';
        $this->formMessage = '';
        $this->formType = 'info';
        $this->formUserId = '';
        $this->formActionUrl = '';
        $this->formActionText = '';
        $this->formScheduledAt = '';
        $this->formData = '';
    }

    public function resetPromotionForm()
    {
        $this->formPromotionId = '';
        $this->formPromotionUserIds = [];
    }

    public function render()
    {
        return view('livewire.admin.notifications.push-notification-management')->layout('layouts.admin');
    }
}