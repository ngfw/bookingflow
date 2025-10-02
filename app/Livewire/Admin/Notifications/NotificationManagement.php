<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NotificationTemplate;
use App\Models\NotificationLog;
use App\Services\NotificationService;
use Carbon\Carbon;

class NotificationManagement extends Component
{
    use WithPagination;

    public $showTemplateModal = false;
    public $showLogModal = false;
    public $selectedTemplate = null;
    public $selectedLog = null;
    public $search = '';
    public $typeFilter = 'all';
    public $statusFilter = 'all';
    public $dateFilter = 'all';

    // Template form fields
    public $name, $type, $event, $subject, $body, $variables = [], $is_active = true, $is_default = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|in:email,sms,push',
        'event' => 'required|string|max:255',
        'subject' => 'nullable|string|max:255',
        'body' => 'required|string',
        'variables' => 'nullable|array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function mount()
    {
        // Create default templates if they don't exist
        NotificationTemplate::createDefaultTemplates();
    }

    public function getTemplates()
    {
        $query = NotificationTemplate::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('event', 'like', '%' . $this->search . '%');
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        return $query->orderBy('type')->orderBy('event')->paginate(10);
    }

    public function getNotificationLogs()
    {
        $query = NotificationLog::with('template');

        if ($this->search) {
            $query->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('body', 'like', '%' . $this->search . '%');
        }

        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter !== 'all') {
            switch ($this->dateFilter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', Carbon::now()->subMonth());
                    break;
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    public function getNotificationStats()
    {
        $today = Carbon::today();
        $week = Carbon::now()->subWeek();
        $month = Carbon::now()->subMonth();

        return [
            'today' => [
                'total' => NotificationLog::whereDate('created_at', $today)->count(),
                'sent' => NotificationLog::whereDate('created_at', $today)->where('status', 'sent')->count(),
                'delivered' => NotificationLog::whereDate('created_at', $today)->where('status', 'delivered')->count(),
                'failed' => NotificationLog::whereDate('created_at', $today)->where('status', 'failed')->count(),
            ],
            'week' => [
                'total' => NotificationLog::where('created_at', '>=', $week)->count(),
                'sent' => NotificationLog::where('created_at', '>=', $week)->where('status', 'sent')->count(),
                'delivered' => NotificationLog::where('created_at', '>=', $week)->where('status', 'delivered')->count(),
                'failed' => NotificationLog::where('created_at', '>=', $week)->where('status', 'failed')->count(),
            ],
            'month' => [
                'total' => NotificationLog::where('created_at', '>=', $month)->count(),
                'sent' => NotificationLog::where('created_at', '>=', $month)->where('status', 'sent')->count(),
                'delivered' => NotificationLog::where('created_at', '>=', $month)->where('status', 'delivered')->count(),
                'failed' => NotificationLog::where('created_at', '>=', $month)->where('status', 'failed')->count(),
            ],
        ];
    }

    public function createTemplate()
    {
        $this->resetInputFields();
        $this->showTemplateModal = true;
    }

    public function editTemplate(NotificationTemplate $template)
    {
        $this->selectedTemplate = $template;
        $this->name = $template->name;
        $this->type = $template->type;
        $this->event = $template->event;
        $this->subject = $template->subject;
        $this->body = $template->body;
        $this->variables = $template->variables ?? [];
        $this->is_active = $template->is_active;
        $this->is_default = $template->is_default;
        $this->showTemplateModal = true;
    }

    public function storeTemplate()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'event' => $this->event,
            'subject' => $this->subject,
            'body' => $this->body,
            'variables' => $this->variables,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
        ];

        if ($this->selectedTemplate) {
            $this->selectedTemplate->update($data);
            session()->flash('message', 'Template updated successfully.');
        } else {
            NotificationTemplate::create($data);
            session()->flash('message', 'Template created successfully.');
        }

        $this->closeTemplateModal();
    }

    public function deleteTemplate(NotificationTemplate $template)
    {
        if ($template->is_default) {
            session()->flash('error', 'Cannot delete default template.');
            return;
        }

        $template->delete();
        session()->flash('message', 'Template deleted successfully.');
    }

    public function toggleTemplateStatus(NotificationTemplate $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        session()->flash('message', 'Template status updated.');
    }

    public function viewLog(NotificationLog $log)
    {
        $this->selectedLog = $log;
        $this->showLogModal = true;
    }

    public function retryNotification(NotificationLog $log)
    {
        $notificationService = new NotificationService();
        
        try {
            $success = false;
            switch ($log->type) {
                case 'email':
                    $success = (new \App\Services\EmailNotificationService())->send($log);
                    break;
                case 'sms':
                    $success = (new \App\Services\SmsNotificationService())->send($log);
                    break;
            }

            if ($success) {
                $log->markAsSent();
                session()->flash('message', 'Notification retried successfully.');
            } else {
                session()->flash('error', 'Failed to retry notification.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error retrying notification: ' . $e->getMessage());
        }
    }

    public function processPendingNotifications()
    {
        $notificationService = new NotificationService();
        $notificationService->processPendingNotifications();
        session()->flash('message', 'Pending notifications processed.');
    }

    public function retryFailedNotifications()
    {
        $notificationService = new NotificationService();
        $notificationService->retryFailedNotifications();
        session()->flash('message', 'Failed notifications retried.');
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
        $this->resetInputFields();
    }

    public function closeLogModal()
    {
        $this->showLogModal = false;
        $this->selectedLog = null;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->type = 'email';
        $this->event = '';
        $this->subject = '';
        $this->body = '';
        $this->variables = [];
        $this->is_active = true;
        $this->is_default = false;
        $this->selectedTemplate = null;
    }

    public function render()
    {
        $templates = $this->getTemplates();
        $logs = $this->getNotificationLogs();
        $stats = $this->getNotificationStats();

        return view('livewire.admin.notifications.notification-management', [
            'templates' => $templates,
            'logs' => $logs,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}
