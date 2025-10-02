<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class ClientCommunicationHistory extends Model
{
    use HasFactory;

    protected $table = 'client_communication_history';

    protected $fillable = [
        'client_id',
        'staff_id',
        'appointment_id',
        'communication_type',
        'direction',
        'subject',
        'message',
        'status',
        'channel',
        'recipient',
        'sender',
        'metadata',
        'sent_at',
        'delivered_at',
        'read_at',
        'notes',
        'is_important',
        'requires_follow_up',
        'follow_up_date',
        'follow_up_notes',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
            'follow_up_date' => 'datetime',
            'is_important' => 'boolean',
            'requires_follow_up' => 'boolean',
        ];
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Helper methods
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => 'failed']);
    }

    public function markAsImportant()
    {
        $this->update(['is_important' => true]);
    }

    public function markAsUnimportant()
    {
        $this->update(['is_important' => false]);
    }

    public function setFollowUp($date, $notes = null)
    {
        $this->update([
            'requires_follow_up' => true,
            'follow_up_date' => $date,
            'follow_up_notes' => $notes,
        ]);
    }

    public function clearFollowUp()
    {
        $this->update([
            'requires_follow_up' => false,
            'follow_up_date' => null,
            'follow_up_notes' => null,
        ]);
    }

    public function isRead()
    {
        return $this->status === 'read';
    }

    public function isDelivered()
    {
        return in_array($this->status, ['delivered', 'read']);
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isOverdue()
    {
        return $this->requires_follow_up && 
               $this->follow_up_date && 
               $this->follow_up_date->isPast();
    }

    public function getStatusDisplayAttribute()
    {
        return match($this->status) {
            'sent' => 'Sent',
            'delivered' => 'Delivered',
            'read' => 'Read',
            'failed' => 'Failed',
            'pending' => 'Pending',
            default => 'Unknown'
        };
    }

    public function getCommunicationTypeDisplayAttribute()
    {
        return match($this->communication_type) {
            'email' => 'Email',
            'sms' => 'SMS',
            'phone' => 'Phone Call',
            'in_person' => 'In Person',
            'push_notification' => 'Push Notification',
            'system_generated' => 'System Generated',
            default => 'Unknown'
        };
    }

    public function getDirectionDisplayAttribute()
    {
        return match($this->direction) {
            'inbound' => 'Inbound',
            'outbound' => 'Outbound',
            default => 'Unknown'
        };
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Static methods for communication management
    public static function logEmail($clientId, $subject, $message, $options = [])
    {
        return self::create([
            'client_id' => $clientId,
            'staff_id' => $options['staff_id'] ?? null,
            'appointment_id' => $options['appointment_id'] ?? null,
            'communication_type' => 'email',
            'direction' => 'outbound',
            'subject' => $subject,
            'message' => $message,
            'status' => $options['status'] ?? 'sent',
            'channel' => 'email',
            'recipient' => $options['recipient'] ?? null,
            'sender' => $options['sender'] ?? 'System',
            'metadata' => $options['metadata'] ?? null,
            'sent_at' => $options['sent_at'] ?? now(),
            'notes' => $options['notes'] ?? null,
            'is_important' => $options['is_important'] ?? false,
        ]);
    }

    public static function logSms($clientId, $message, $options = [])
    {
        return self::create([
            'client_id' => $clientId,
            'staff_id' => $options['staff_id'] ?? null,
            'appointment_id' => $options['appointment_id'] ?? null,
            'communication_type' => 'sms',
            'direction' => 'outbound',
            'message' => $message,
            'status' => $options['status'] ?? 'sent',
            'channel' => 'sms',
            'recipient' => $options['recipient'] ?? null,
            'sender' => $options['sender'] ?? 'System',
            'metadata' => $options['metadata'] ?? null,
            'sent_at' => $options['sent_at'] ?? now(),
            'notes' => $options['notes'] ?? null,
            'is_important' => $options['is_important'] ?? false,
        ]);
    }

    public static function logPhoneCall($clientId, $message, $options = [])
    {
        return self::create([
            'client_id' => $clientId,
            'staff_id' => $options['staff_id'] ?? null,
            'appointment_id' => $options['appointment_id'] ?? null,
            'communication_type' => 'phone',
            'direction' => $options['direction'] ?? 'outbound',
            'message' => $message,
            'status' => 'delivered', // Phone calls are considered delivered when completed
            'channel' => 'phone',
            'recipient' => $options['recipient'] ?? null,
            'sender' => $options['sender'] ?? 'Staff',
            'metadata' => $options['metadata'] ?? null,
            'sent_at' => $options['sent_at'] ?? now(),
            'notes' => $options['notes'] ?? null,
            'is_important' => $options['is_important'] ?? false,
        ]);
    }

    public static function logInPersonCommunication($clientId, $message, $options = [])
    {
        return self::create([
            'client_id' => $clientId,
            'staff_id' => $options['staff_id'] ?? null,
            'appointment_id' => $options['appointment_id'] ?? null,
            'communication_type' => 'in_person',
            'direction' => 'outbound',
            'message' => $message,
            'status' => 'delivered',
            'channel' => 'in_person',
            'sender' => $options['sender'] ?? 'Staff',
            'metadata' => $options['metadata'] ?? null,
            'sent_at' => $options['sent_at'] ?? now(),
            'notes' => $options['notes'] ?? null,
            'is_important' => $options['is_important'] ?? false,
        ]);
    }

    public static function logPushNotification($clientId, $subject, $message, $options = [])
    {
        return self::create([
            'client_id' => $clientId,
            'staff_id' => $options['staff_id'] ?? null,
            'appointment_id' => $options['appointment_id'] ?? null,
            'communication_type' => 'push_notification',
            'direction' => 'outbound',
            'subject' => $subject,
            'message' => $message,
            'status' => $options['status'] ?? 'sent',
            'channel' => 'push',
            'recipient' => $options['recipient'] ?? null,
            'sender' => $options['sender'] ?? 'System',
            'metadata' => $options['metadata'] ?? null,
            'sent_at' => $options['sent_at'] ?? now(),
            'notes' => $options['notes'] ?? null,
            'is_important' => $options['is_important'] ?? false,
        ]);
    }

    public static function getCommunicationHistoryForClient($clientId, $limit = 50)
    {
        return self::where('client_id', $clientId)
            ->with(['staff.user', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getOverdueFollowUps()
    {
        return self::where('requires_follow_up', true)
            ->where('follow_up_date', '<', now())
            ->with(['client', 'staff.user'])
            ->orderBy('follow_up_date')
            ->get();
    }

    public static function getImportantCommunications($limit = 20)
    {
        return self::where('is_important', true)
            ->with(['client', 'staff.user', 'appointment'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public static function getCommunicationStatistics($startDate = null, $endDate = null)
    {
        $query = self::query();

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return [
            'total' => $query->count(),
            'email' => $query->where('communication_type', 'email')->count(),
            'sms' => $query->where('communication_type', 'sms')->count(),
            'phone' => $query->where('communication_type', 'phone')->count(),
            'in_person' => $query->where('communication_type', 'in_person')->count(),
            'push_notification' => $query->where('communication_type', 'push_notification')->count(),
            'inbound' => $query->where('direction', 'inbound')->count(),
            'outbound' => $query->where('direction', 'outbound')->count(),
            'important' => $query->where('is_important', true)->count(),
            'follow_up_required' => $query->where('requires_follow_up', true)->count(),
        ];
    }
}