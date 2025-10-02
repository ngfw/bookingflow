<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FranchiseCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'franchise_id',
        'sender_id',
        'communication_type',
        'subject',
        'message',
        'priority',
        'status',
        'read_at',
        'responded_at',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
            'responded_at' => 'datetime',
            'attachments' => 'array',
        ];
    }

    public function franchise()
    {
        return $this->belongsTo(Franchise::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    public function markAsResponded()
    {
        $this->update([
            'status' => 'responded',
            'responded_at' => now(),
        ]);
    }

    public function isUrgent()
    {
        return $this->priority === 'urgent';
    }

    public function isHighPriority()
    {
        return in_array($this->priority, ['high', 'urgent']);
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('communication_type', $type);
    }
}