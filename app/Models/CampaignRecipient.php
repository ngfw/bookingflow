<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'client_id',
        'email',
        'status',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'unsubscribed_at',
        'error_message',
        'tracking_data',
    ];

    protected function casts(): array
    {
        return [
            'tracking_data' => 'array',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'opened_at' => 'datetime',
            'clicked_at' => 'datetime',
            'bounced_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsOpened(): void
    {
        if ($this->status === 'delivered' || $this->status === 'sent') {
            $this->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);
        }
    }

    public function markAsClicked(): void
    {
        if (in_array($this->status, ['delivered', 'opened', 'sent'])) {
            $this->update([
                'status' => 'clicked',
                'clicked_at' => now(),
            ]);
        }
    }

    public function markAsBounced(string $errorMessage = null): void
    {
        $this->update([
            'status' => 'bounced',
            'bounced_at' => now(),
            'error_message' => $errorMessage,
        ]);
    }

    public function markAsUnsubscribed(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'pending' => 'bg-gray-100 text-gray-800',
            'sent' => 'bg-blue-100 text-blue-800',
            'delivered' => 'bg-green-100 text-green-800',
            'opened' => 'bg-purple-100 text-purple-800',
            'clicked' => 'bg-indigo-100 text-indigo-800',
            'bounced' => 'bg-red-100 text-red-800',
            'unsubscribed' => 'bg-orange-100 text-orange-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->status) {
            'pending' => '⏳',
            'sent' => '📤',
            'delivered' => '✅',
            'opened' => '👁️',
            'clicked' => '🖱️',
            'bounced' => '❌',
            'unsubscribed' => '🚫',
            default => '⏳',
        };
    }

    public function isEngaged(): bool
    {
        return in_array($this->status, ['opened', 'clicked']);
    }

    public function hasBounced(): bool
    {
        return $this->status === 'bounced';
    }

    public function hasUnsubscribed(): bool
    {
        return $this->status === 'unsubscribed';
    }
}