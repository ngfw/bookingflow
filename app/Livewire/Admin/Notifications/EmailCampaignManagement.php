<?php

namespace App\Livewire\Admin\Notifications;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EmailCampaign;
use App\Models\CampaignRecipient;
use App\Models\Client;
use App\Models\Service;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;

class EmailCampaignManagement extends Component
{
    use WithPagination;

    public $showCampaignModal = false;
    public $showPreviewModal = false;
    public $showRecipientsModal = false;
    public $showStatsModal = false;
    public $selectedCampaign = null;
    public $search = '';
    public $statusFilter = 'all';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Campaign form fields
    public $name, $description, $subject, $content, $template_type = 'html', $target_criteria = [], $scheduled_at, $settings = [];

    // Preview and stats
    public $previewContent = '';
    public $campaignStats = [];
    public $recipients = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'subject' => 'required|string|max:255',
        'content' => 'required|string',
        'template_type' => 'required|in:html,text,markdown',
        'target_criteria' => 'nullable|array',
        'scheduled_at' => 'nullable|date|after:now',
        'settings' => 'nullable|array',
    ];

    public function mount()
    {
        $this->settings = [
            'track_opens' => true,
            'track_clicks' => true,
            'unsubscribe_link' => true,
            'from_name' => config('app.name', 'BookingFlow'),
            'from_email' => config('mail.from.address'),
        ];
    }

    public function getCampaigns()
    {
        $query = EmailCampaign::with(['creator', 'recipients']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
    }

    public function getCampaignStats()
    {
        $totalCampaigns = EmailCampaign::count();
        $activeCampaigns = EmailCampaign::whereIn('status', ['draft', 'scheduled', 'sending'])->count();
        $sentCampaigns = EmailCampaign::where('status', 'sent')->count();
        
        $totalRecipients = CampaignRecipient::count();
        $deliveredEmails = CampaignRecipient::where('status', 'delivered')->count();
        $openedEmails = CampaignRecipient::where('status', 'opened')->count();
        $clickedEmails = CampaignRecipient::where('status', 'clicked')->count();

        $avgOpenRate = $deliveredEmails > 0 ? round(($openedEmails / $deliveredEmails) * 100, 2) : 0;
        $avgClickRate = $deliveredEmails > 0 ? round(($clickedEmails / $deliveredEmails) * 100, 2) : 0;

        return [
            'total_campaigns' => $totalCampaigns,
            'active_campaigns' => $activeCampaigns,
            'sent_campaigns' => $sentCampaigns,
            'total_recipients' => $totalRecipients,
            'delivered_emails' => $deliveredEmails,
            'opened_emails' => $openedEmails,
            'clicked_emails' => $clickedEmails,
            'avg_open_rate' => $avgOpenRate,
            'avg_click_rate' => $avgClickRate,
        ];
    }

    public function getRecentCampaigns()
    {
        return EmailCampaign::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function createCampaign()
    {
        $this->resetInputFields();
        $this->showCampaignModal = true;
    }

    public function editCampaign(EmailCampaign $campaign)
    {
        $this->selectedCampaign = $campaign;
        $this->name = $campaign->name;
        $this->description = $campaign->description;
        $this->subject = $campaign->subject;
        $this->content = $campaign->content;
        $this->template_type = $campaign->template_type;
        $this->target_criteria = $campaign->target_criteria ?? [];
        $this->scheduled_at = $campaign->scheduled_at?->format('Y-m-d\TH:i');
        $this->settings = $campaign->settings ?? $this->settings;
        $this->showCampaignModal = true;
    }

    public function storeCampaign()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'subject' => $this->subject,
            'content' => $this->content,
            'template_type' => $this->template_type,
            'target_criteria' => $this->target_criteria,
            'scheduled_at' => $this->scheduled_at ? Carbon::parse($this->scheduled_at) : null,
            'settings' => $this->settings,
            'created_by' => auth()->id(),
        ];

        if ($this->selectedCampaign) {
            $this->selectedCampaign->update($data);
            session()->flash('message', 'Campaign updated successfully.');
        } else {
            EmailCampaign::create($data);
            session()->flash('message', 'Campaign created successfully.');
        }

        $this->closeCampaignModal();
    }

    public function deleteCampaign(EmailCampaign $campaign)
    {
        if ($campaign->status === 'sending') {
            session()->flash('error', 'Cannot delete campaign while it is being sent.');
            return;
        }

        $campaign->delete();
        session()->flash('message', 'Campaign deleted successfully.');
    }

    public function previewCampaign(EmailCampaign $campaign)
    {
        $this->selectedCampaign = $campaign;
        $this->previewContent = $campaign->getPreviewContent();
        $this->showPreviewModal = true;
    }

    public function viewRecipients(EmailCampaign $campaign)
    {
        $this->selectedCampaign = $campaign;
        $this->recipients = $campaign->recipients()->with('client')->paginate(20);
        $this->showRecipientsModal = true;
    }

    public function viewStats(EmailCampaign $campaign)
    {
        $this->selectedCampaign = $campaign;
        $this->campaignStats = [
            'total_recipients' => $campaign->total_recipients,
            'sent_count' => $campaign->sent_count,
            'delivered_count' => $campaign->delivered_count,
            'opened_count' => $campaign->opened_count,
            'clicked_count' => $campaign->clicked_count,
            'bounced_count' => $campaign->bounced_count,
            'unsubscribed_count' => $campaign->unsubscribed_count,
            'open_rate' => $campaign->open_rate,
            'click_rate' => $campaign->click_rate,
            'bounce_rate' => $campaign->bounce_rate,
            'unsubscribe_rate' => $campaign->unsubscribe_rate,
        ];
        $this->showStatsModal = true;
    }

    public function scheduleCampaign(EmailCampaign $campaign)
    {
        if (!$campaign->canBeScheduled()) {
            session()->flash('error', 'Campaign cannot be scheduled.');
            return;
        }

        $campaign->update(['status' => 'scheduled']);
        session()->flash('message', 'Campaign scheduled successfully.');
    }

    public function sendCampaign(EmailCampaign $campaign)
    {
        if (!$campaign->canBeSent()) {
            session()->flash('error', 'Campaign cannot be sent.');
            return;
        }

        try {
            $campaign->update(['status' => 'sending']);
            
            // Get target clients
            $clients = $campaign->getTargetClients();
            $campaign->update(['total_recipients' => $clients->count()]);

            // Create recipient records
            foreach ($clients as $client) {
                CampaignRecipient::create([
                    'campaign_id' => $campaign->id,
                    'client_id' => $client->id,
                    'email' => $client->email,
                ]);
            }

            // Send emails (simulate for now)
            $this->simulateEmailSending($campaign);

            session()->flash('message', 'Campaign sent successfully.');
        } catch (\Exception $e) {
            $campaign->update(['status' => 'draft']);
            session()->flash('error', 'Error sending campaign: ' . $e->getMessage());
        }
    }

    protected function simulateEmailSending(EmailCampaign $campaign)
    {
        $recipients = $campaign->recipients;
        
        foreach ($recipients as $recipient) {
            // Simulate email sending
            $recipient->markAsSent();
            
            // Simulate delivery (90% success rate)
            if (rand(1, 10) <= 9) {
                $recipient->markAsDelivered();
                
                // Simulate opens (30% of delivered)
                if (rand(1, 10) <= 3) {
                    $recipient->markAsOpened();
                    
                    // Simulate clicks (10% of opened)
                    if (rand(1, 10) <= 1) {
                        $recipient->markAsClicked();
                    }
                }
            } else {
                $recipient->markAsBounced('Simulated bounce');
            }
        }

        // Update campaign stats
        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $recipients->where('status', 'sent')->count(),
            'delivered_count' => $recipients->where('status', 'delivered')->count(),
            'opened_count' => $recipients->where('status', 'opened')->count(),
            'clicked_count' => $recipients->where('status', 'clicked')->count(),
            'bounced_count' => $recipients->where('status', 'bounced')->count(),
        ]);
    }

    public function pauseCampaign(EmailCampaign $campaign)
    {
        if ($campaign->status === 'sending') {
            $campaign->update(['status' => 'paused']);
            session()->flash('message', 'Campaign paused successfully.');
        }
    }

    public function cancelCampaign(EmailCampaign $campaign)
    {
        if (in_array($campaign->status, ['draft', 'scheduled', 'paused'])) {
            $campaign->update(['status' => 'cancelled']);
            session()->flash('message', 'Campaign cancelled successfully.');
        }
    }

    public function duplicateCampaign(EmailCampaign $campaign)
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (Copy)';
        $newCampaign->status = 'draft';
        $newCampaign->scheduled_at = null;
        $newCampaign->sent_at = null;
        $newCampaign->total_recipients = 0;
        $newCampaign->sent_count = 0;
        $newCampaign->delivered_count = 0;
        $newCampaign->opened_count = 0;
        $newCampaign->clicked_count = 0;
        $newCampaign->unsubscribed_count = 0;
        $newCampaign->bounced_count = 0;
        $newCampaign->created_by = auth()->id();
        $newCampaign->save();

        session()->flash('message', 'Campaign duplicated successfully.');
    }

    public function closeCampaignModal()
    {
        $this->showCampaignModal = false;
        $this->resetInputFields();
    }

    public function closePreviewModal()
    {
        $this->showPreviewModal = false;
    }

    public function closeRecipientsModal()
    {
        $this->showRecipientsModal = false;
    }

    public function closeStatsModal()
    {
        $this->showStatsModal = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->description = '';
        $this->subject = '';
        $this->content = '';
        $this->template_type = 'html';
        $this->target_criteria = [];
        $this->scheduled_at = '';
        $this->selectedCampaign = null;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getServices()
    {
        return Service::all();
    }

    public function getStaff()
    {
        return User::where('role', 'staff')->get();
    }

    public function render()
    {
        $campaigns = $this->getCampaigns();
        $stats = $this->getCampaignStats();
        $recentCampaigns = $this->getRecentCampaigns();
        $services = $this->getServices();
        $staff = $this->getStaff();

        return view('livewire.admin.notifications.email-campaign-management', [
            'campaigns' => $campaigns,
            'stats' => $stats,
            'recentCampaigns' => $recentCampaigns,
            'services' => $services,
            'staff' => $staff,
        ])->layout('layouts.admin');
    }
}
