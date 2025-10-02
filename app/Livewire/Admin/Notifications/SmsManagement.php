<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\NotificationLog;
use App\Services\SmsNotificationService;
use Carbon\Carbon;

class SmsManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateFilter = 'all';
    public $showStatsModal = false;
    public $showTestModal = false;
    public $testPhoneNumber = '';
    public $testMessage = '';

    public function getSmsLogs()
    {
        $query = NotificationLog::where('type', 'sms')->with('template');

        if ($this->search) {
            $query->where('body', 'like', '%' . $this->search . '%')
                  ->orWhere('recipient_phone', 'like', '%' . $this->search . '%');
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

    public function getSmsStats()
    {
        $today = Carbon::today();
        $week = Carbon::now()->subWeek();
        $month = Carbon::now()->subMonth();

        return [
            'today' => [
                'total' => NotificationLog::where('type', 'sms')->whereDate('created_at', $today)->count(),
                'sent' => NotificationLog::where('type', 'sms')->whereDate('created_at', $today)->where('status', 'sent')->count(),
                'delivered' => NotificationLog::where('type', 'sms')->whereDate('created_at', $today)->where('status', 'delivered')->count(),
                'failed' => NotificationLog::where('type', 'sms')->whereDate('created_at', $today)->where('status', 'failed')->count(),
            ],
            'week' => [
                'total' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $week)->count(),
                'sent' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $week)->where('status', 'sent')->count(),
                'delivered' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $week)->where('status', 'delivered')->count(),
                'failed' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $week)->where('status', 'failed')->count(),
            ],
            'month' => [
                'total' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $month)->count(),
                'sent' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $month)->where('status', 'sent')->count(),
                'delivered' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $month)->where('status', 'delivered')->count(),
                'failed' => NotificationLog::where('type', 'sms')->where('created_at', '>=', $month)->where('status', 'failed')->count(),
            ],
        ];
    }

    public function getTwilioStats()
    {
        $smsService = new SmsNotificationService();
        
        return [
            'balance' => $smsService->getAccountBalance(),
            'usage' => $smsService->getUsageStats(),
            'enabled' => config('services.twilio.enabled', false),
        ];
    }

    public function retrySms(NotificationLog $log)
    {
        $smsService = new SmsNotificationService();
        
        try {
            $success = $smsService->send($log);
            
            if ($success) {
                session()->flash('message', 'SMS retried successfully.');
            } else {
                session()->flash('error', 'Failed to retry SMS.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error retrying SMS: ' . $e->getMessage());
        }
    }

    public function updateSmsStatus(NotificationLog $log)
    {
        $smsService = new SmsNotificationService();
        
        try {
            $updated = $smsService->updateNotificationStatus($log);
            
            if ($updated) {
                session()->flash('message', 'SMS status updated successfully.');
            } else {
                session()->flash('error', 'Failed to update SMS status.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error updating SMS status: ' . $e->getMessage());
        }
    }

    public function sendTestSms()
    {
        $this->validate([
            'testPhoneNumber' => 'required|string|min:10',
            'testMessage' => 'required|string|max:160',
        ]);

        $smsService = new SmsNotificationService();
        
        try {
            // Create a test notification log
            $log = NotificationLog::create([
                'template_id' => null,
                'type' => 'sms',
                'event' => 'test',
                'recipient_type' => 'client',
                'recipient_id' => 0,
                'recipient_phone' => $this->testPhoneNumber,
                'subject' => null,
                'body' => $this->testMessage,
                'status' => 'pending',
                'metadata' => ['test_message' => true],
            ]);

            $success = $smsService->send($log);
            
            if ($success) {
                session()->flash('message', 'Test SMS sent successfully.');
                $this->reset(['testPhoneNumber', 'testMessage']);
                $this->showTestModal = false;
            } else {
                session()->flash('error', 'Failed to send test SMS.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error sending test SMS: ' . $e->getMessage());
        }
    }

    public function showStatsModal()
    {
        $this->showStatsModal = true;
    }

    public function hideStatsModal()
    {
        $this->showStatsModal = false;
    }

    public function showTestModal()
    {
        $this->showTestModal = true;
    }

    public function hideTestModal()
    {
        $this->showTestModal = false;
        $this->reset(['testPhoneNumber', 'testMessage']);
    }

    public function render()
    {
        $logs = $this->getSmsLogs();
        $stats = $this->getSmsStats();
        $twilioStats = $this->getTwilioStats();

        return view('livewire.admin.notifications.sms-management', [
            'logs' => $logs,
            'stats' => $stats,
            'twilioStats' => $twilioStats,
        ])->layout('layouts.admin');
    }
}
