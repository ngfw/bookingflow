<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class SmsNotificationService
{
    protected $client;
    protected $fromNumber;
    protected $isEnabled;

    public function __construct()
    {
        $this->isEnabled = config('services.twilio.enabled', false);
        
        if ($this->isEnabled) {
            $this->client = new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
            $this->fromNumber = config('services.twilio.from');
        }
    }

    public function send(NotificationLog $notification)
    {
        try {
            $recipient = $this->getRecipient($notification);
            
            if (!$recipient || !$recipient->phone) {
                $notification->markAsFailed('No phone number found');
                return false;
            }

            $phoneNumber = $this->formatPhoneNumber($recipient->phone);
            
            if (!$this->validatePhoneNumber($phoneNumber)) {
                $notification->markAsFailed('Invalid phone number format');
                return false;
            }

            if ($this->isEnabled) {
                return $this->sendWithTwilio($notification, $phoneNumber);
            } else {
                return $this->simulateSmsSending($notification, $recipient);
            }

        } catch (\Exception $e) {
            Log::error("SMS sending failed: " . $e->getMessage());
            $notification->markAsFailed($e->getMessage());
            return false;
        }
    }

    protected function sendWithTwilio(NotificationLog $notification, $phoneNumber)
    {
        try {
            $message = $this->client->messages->create(
                $phoneNumber,
                [
                    'from' => $this->fromNumber,
                    'body' => $notification->body
                ]
            );

            // Update notification with Twilio message SID
            $notification->update([
                'metadata' => array_merge($notification->metadata ?? [], [
                    'twilio_sid' => $message->sid,
                    'twilio_status' => $message->status,
                ])
            ]);

            Log::info("SMS sent successfully via Twilio to {$phoneNumber}. SID: {$message->sid}");
            return true;

        } catch (TwilioException $e) {
            Log::error("Twilio SMS sending failed: " . $e->getMessage());
            $notification->markAsFailed("Twilio error: " . $e->getMessage());
            return false;
        }
    }

    protected function simulateSmsSending(NotificationLog $notification, $recipient)
    {
        // Simulate SMS sending delay
        sleep(1);
        
        // Log the SMS content for testing
        Log::info("SMS Content for {$recipient->phone}: {$notification->body}");
        
        // Simulate different outcomes for testing
        $random = rand(1, 100);
        if ($random <= 95) {
            // 95% success rate
            return true;
        } else {
            // 5% failure rate for testing
            $notification->markAsFailed('Simulated failure for testing');
            return false;
        }
    }

    public function getMessageStatus($messageSid)
    {
        if (!$this->isEnabled) {
            return null;
        }

        try {
            $message = $this->client->messages($messageSid)->fetch();
            return [
                'sid' => $message->sid,
                'status' => $message->status,
                'error_code' => $message->errorCode,
                'error_message' => $message->errorMessage,
                'price' => $message->price,
                'price_unit' => $message->priceUnit,
                'date_sent' => $message->dateSent,
                'date_updated' => $message->dateUpdated,
            ];
        } catch (TwilioException $e) {
            Log::error("Failed to fetch message status: " . $e->getMessage());
            return null;
        }
    }

    public function updateNotificationStatus(NotificationLog $notification)
    {
        if (!$this->isEnabled || !isset($notification->metadata['twilio_sid'])) {
            return false;
        }

        $status = $this->getMessageStatus($notification->metadata['twilio_sid']);
        
        if ($status) {
            switch ($status['status']) {
                case 'delivered':
                    $notification->markAsDelivered();
                    break;
                case 'failed':
                case 'undelivered':
                    $notification->markAsFailed($status['error_message'] ?? 'Message failed');
                    break;
                case 'sent':
                    $notification->markAsSent();
                    break;
            }

            // Update metadata with latest status
            $notification->update([
                'metadata' => array_merge($notification->metadata ?? [], [
                    'twilio_status' => $status['status'],
                    'twilio_error_code' => $status['error_code'],
                    'twilio_error_message' => $status['error_message'],
                    'twilio_price' => $status['price'],
                    'last_status_check' => now(),
                ])
            ]);

            return true;
        }

        return false;
    }

    public function sendBulk($notifications)
    {
        $results = [];
        
        foreach ($notifications as $notification) {
            $results[] = $this->send($notification);
        }
        
        return $results;
    }

    public function sendWithTemplate($template, $recipient, $data = [])
    {
        try {
            $rendered = $template->renderTemplate($data);
            
            // Create a temporary notification log for template sending
            $notification = new NotificationLog([
                'body' => $rendered['body'],
                'recipient_type' => 'client',
                'recipient_id' => $recipient->id,
                'recipient_phone' => $recipient->phone,
            ]);
            
            return $this->send($notification);

        } catch (\Exception $e) {
            Log::error("Template SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    public function validatePhoneNumber($phone)
    {
        // Basic phone number validation
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return strlen($phone) >= 10 && strlen($phone) <= 15;
    }

    public function formatPhoneNumber($phone)
    {
        // Format phone number for SMS sending
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        if (strpos($phone, '+') !== 0) {
            $phone = '+1' . $phone; // Default to US country code
        }
        
        return $phone;
    }

    public function getAccountBalance()
    {
        if (!$this->isEnabled) {
            return null;
        }

        try {
            $balance = $this->client->balance->fetch();
            return [
                'currency' => $balance->currency,
                'balance' => $balance->balance,
            ];
        } catch (TwilioException $e) {
            Log::error("Failed to fetch account balance: " . $e->getMessage());
            return null;
        }
    }

    public function getUsageStats($startDate = null, $endDate = null)
    {
        if (!$this->isEnabled) {
            return null;
        }

        try {
            $usage = $this->client->usage->records->read([
                'startDate' => $startDate ?? date('Y-m-01'),
                'endDate' => $endDate ?? date('Y-m-d'),
            ]);

            $stats = [
                'total_messages' => 0,
                'total_cost' => 0,
                'by_category' => [],
            ];

            foreach ($usage as $record) {
                $stats['total_messages'] += $record->count;
                $stats['total_cost'] += $record->price;
                
                if (!isset($stats['by_category'][$record->category])) {
                    $stats['by_category'][$record->category] = [
                        'count' => 0,
                        'cost' => 0,
                    ];
                }
                
                $stats['by_category'][$record->category]['count'] += $record->count;
                $stats['by_category'][$record->category]['cost'] += $record->price;
            }

            return $stats;
        } catch (TwilioException $e) {
            Log::error("Failed to fetch usage stats: " . $e->getMessage());
            return null;
        }
    }

    protected function getRecipient($notification)
    {
        switch ($notification->recipient_type) {
            case 'client':
                return \App\Models\Client::find($notification->recipient_id);
            case 'staff':
            case 'admin':
                return \App\Models\User::find($notification->recipient_id);
            default:
                return null;
        }
    }
}
