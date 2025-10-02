<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject',
        'content',
        'template_type',
        'target_criteria',
        'status',
        'scheduled_at',
        'sent_at',
        'total_recipients',
        'sent_count',
        'delivered_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
        'bounced_count',
        'settings',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'target_criteria' => 'array',
            'settings' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(CampaignRecipient::class);
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->delivered_count == 0) {
            return 0;
        }
        return round(($this->opened_count / $this->delivered_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->delivered_count == 0) {
            return 0;
        }
        return round(($this->clicked_count / $this->delivered_count) * 100, 2);
    }

    public function getBounceRateAttribute(): float
    {
        if ($this->sent_count == 0) {
            return 0;
        }
        return round(($this->bounced_count / $this->sent_count) * 100, 2);
    }

    public function getUnsubscribeRateAttribute(): float
    {
        if ($this->delivered_count == 0) {
            return 0;
        }
        return round(($this->unsubscribed_count / $this->delivered_count) * 100, 2);
    }

    public function canBeSent(): bool
    {
        return $this->status === 'draft' || $this->status === 'scheduled';
    }

    public function canBeScheduled(): bool
    {
        return $this->status === 'draft' && $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function getTargetClients()
    {
        $query = Client::query();

        if ($this->target_criteria) {
            foreach ($this->target_criteria as $criteria => $value) {
                switch ($criteria) {
                    case 'client_types':
                        $query->whereIn('type', $value);
                        break;
                    case 'last_appointment_days':
                        $query->whereHas('appointments', function($q) use ($value) {
                            $q->where('appointment_date', '>=', now()->subDays($value));
                        });
                        break;
                    case 'total_spent_min':
                        $query->whereHas('appointments', function($q) use ($value) {
                            $q->whereHas('invoice', function($invoiceQ) use ($value) {
                                $invoiceQ->where('total_amount', '>=', $value);
                            });
                        });
                        break;
                    case 'services_used':
                        $query->whereHas('appointments', function($q) use ($value) {
                            $q->whereIn('service_id', $value);
                        });
                        break;
                    case 'staff_preference':
                        $query->whereHas('appointments', function($q) use ($value) {
                            $q->whereIn('staff_id', $value);
                        });
                        break;
                    case 'location':
                        $query->where('city', 'like', '%' . $value . '%');
                        break;
                    case 'age_range':
                        if (isset($value['min'])) {
                            $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= ?', [$value['min']]);
                        }
                        if (isset($value['max'])) {
                            $query->whereRaw('TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) <= ?', [$value['max']]);
                        }
                        break;
                }
            }
        }

        return $query->where('email_notifications', true)->get();
    }

    public function getPreviewContent(): string
    {
        $content = $this->content;
        
        // Replace placeholders with sample data
        $placeholders = [
            '{{client_name}}' => 'John Doe',
            '{{salon_name}}' => config('app.name', 'Beauty Salon'),
            '{{salon_phone}}' => '+1 (555) 123-4567',
            '{{salon_email}}' => 'info@beautysalon.com',
            '{{salon_address}}' => '123 Beauty Street, City, State 12345',
            '{{current_date}}' => now()->format('M d, Y'),
            '{{current_year}}' => now()->year,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $content);
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'sending' => 'bg-yellow-100 text-yellow-800',
            'sent' => 'bg-green-100 text-green-800',
            'paused' => 'bg-orange-100 text-orange-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusIcon(): string
    {
        return match($this->status) {
            'draft' => '📝',
            'scheduled' => '⏰',
            'sending' => '📤',
            'sent' => '✅',
            'paused' => '⏸️',
            'cancelled' => '❌',
            default => '📝',
        };
    }
}