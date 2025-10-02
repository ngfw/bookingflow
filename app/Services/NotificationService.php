<?php

namespace App\Services;

use App\Models\NotificationTemplate;
use App\Models\NotificationLog;
use App\Models\Client;
use App\Models\User;
use App\Models\Appointment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationService
{
    protected $emailService;
    protected $smsService;

    public function __construct()
    {
        $this->emailService = new EmailNotificationService();
        $this->smsService = new SmsNotificationService();
    }

    public function sendAppointmentConfirmation(Appointment $appointment)
    {
        $client = $appointment->client;
        $service = $appointment->service;
        $staff = $appointment->staff;

        $data = [
            'client_name' => $client->name,
            'appointment_date' => $appointment->appointment_date->format('M d, Y'),
            'appointment_time' => $appointment->appointment_time->format('g:i A'),
            'service_name' => $service->name,
            'staff_name' => $staff->name,
            'salon_name' => config('app.name', 'Beauty Salon'),
            'appointment_id' => $appointment->id,
        ];

        // Send email notification
        $this->sendNotification('email', 'appointment_confirmation', 'client', $client->id, $data, [
            'appointment_id' => $appointment->id,
            'client_id' => $client->id,
        ]);

        // Send SMS notification if client has phone
        if ($client->phone) {
            $this->sendNotification('sms', 'appointment_confirmation', 'client', $client->id, $data, [
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
            ]);
        }
    }

    public function sendAppointmentReminder(Appointment $appointment)
    {
        $client = $appointment->client;
        $service = $appointment->service;
        $staff = $appointment->staff;

        $data = [
            'client_name' => $client->name,
            'appointment_date' => $appointment->appointment_date->format('M d, Y'),
            'appointment_time' => $appointment->appointment_time->format('g:i A'),
            'service_name' => $service->name,
            'staff_name' => $staff->name,
            'salon_name' => config('app.name', 'Beauty Salon'),
            'appointment_id' => $appointment->id,
        ];

        // Send email reminder
        $this->sendNotification('email', 'appointment_reminder', 'client', $client->id, $data, [
            'appointment_id' => $appointment->id,
            'client_id' => $client->id,
        ]);

        // Send SMS reminder if client has phone
        if ($client->phone) {
            $this->sendNotification('sms', 'appointment_reminder', 'client', $client->id, $data, [
                'appointment_id' => $appointment->id,
                'client_id' => $client->id,
            ]);
        }
    }

    public function sendPaymentReceipt(Invoice $invoice, $paymentMethod = 'cash')
    {
        $client = $invoice->client;
        $services = $invoice->items->map(function($item) {
            return $item->product->name . ' - $' . number_format($item->total_price, 2);
        })->join("\n");

        $data = [
            'client_name' => $client->name,
            'invoice_number' => $invoice->invoice_number,
            'amount' => number_format($invoice->total_amount, 2),
            'payment_method' => ucfirst($paymentMethod),
            'payment_date' => Carbon::now()->format('M d, Y'),
            'services_list' => $services,
            'salon_name' => config('app.name', 'Beauty Salon'),
            'invoice_id' => $invoice->id,
        ];

        // Send email receipt
        $this->sendNotification('email', 'payment_receipt', 'client', $client->id, $data, [
            'invoice_id' => $invoice->id,
            'client_id' => $client->id,
        ]);

        // Send SMS receipt if client has phone
        if ($client->phone) {
            $this->sendNotification('sms', 'payment_receipt', 'client', $client->id, $data, [
                'invoice_id' => $invoice->id,
                'client_id' => $client->id,
            ]);
        }
    }

    public function sendAppointmentCancellation(Appointment $appointment, $reason = 'Client request')
    {
        $client = $appointment->client;
        $service = $appointment->service;
        $staff = $appointment->staff;

        $data = [
            'client_name' => $client->name,
            'appointment_date' => $appointment->appointment_date->format('M d, Y'),
            'appointment_time' => $appointment->appointment_time->format('g:i A'),
            'service_name' => $service->name,
            'staff_name' => $staff->name,
            'cancellation_reason' => $reason,
            'salon_name' => config('app.name', 'Beauty Salon'),
            'appointment_id' => $appointment->id,
        ];

        // Send email cancellation notice
        $this->sendNotification('email', 'appointment_cancellation', 'client', $client->id, $data, [
            'appointment_id' => $appointment->id,
            'client_id' => $client->id,
        ]);
    }

    public function sendWelcomeEmail(Client $client)
    {
        $data = [
            'client_name' => $client->name,
            'salon_name' => config('app.name', 'Beauty Salon'),
            'client_id' => $client->id,
        ];

        $this->sendNotification('email', 'client_welcome', 'client', $client->id, $data, [
            'client_id' => $client->id,
        ]);
    }

    public function sendCustomNotification($type, $event, $recipientType, $recipientId, $data = [], $metadata = [])
    {
        return $this->sendNotification($type, $event, $recipientType, $recipientId, $data, $metadata);
    }

    protected function sendNotification($type, $event, $recipientType, $recipientId, $data = [], $metadata = [])
    {
        try {
            // Get the notification template
            $template = NotificationTemplate::getTemplate($type, $event);
            
            if (!$template) {
                Log::warning("No template found for {$type}:{$event}");
                return false;
            }

            // Get recipient information
            $recipient = $this->getRecipient($recipientType, $recipientId);
            if (!$recipient) {
                Log::warning("Recipient not found: {$recipientType}:{$recipientId}");
                return false;
            }

            // Render the template
            $rendered = $template->renderTemplate($data);

            // Create notification log
            $log = NotificationLog::create([
                'template_id' => $template->id,
                'type' => $type,
                'event' => $event,
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'recipient_email' => $recipient->email ?? null,
                'recipient_phone' => $recipient->phone ?? null,
                'subject' => $rendered['subject'],
                'body' => $rendered['body'],
                'status' => 'pending',
                'metadata' => $metadata,
            ]);

            // Send the notification
            $success = false;
            switch ($type) {
                case 'email':
                    $success = $this->emailService->send($log);
                    break;
                case 'sms':
                    $success = $this->smsService->send($log);
                    break;
                case 'push':
                    $success = $this->sendPushNotification($log);
                    break;
            }

            if ($success) {
                $log->markAsSent();
            } else {
                $log->markAsFailed('Failed to send notification');
            }

            return $success;

        } catch (\Exception $e) {
            Log::error("Notification sending failed: " . $e->getMessage());
            return false;
        }
    }

    protected function getRecipient($recipientType, $recipientId)
    {
        switch ($recipientType) {
            case 'client':
                return Client::find($recipientId);
            case 'staff':
            case 'admin':
                return User::find($recipientId);
            default:
                return null;
        }
    }

    protected function sendPushNotification(NotificationLog $log)
    {
        // Placeholder for push notification implementation
        // This would integrate with services like Firebase, OneSignal, etc.
        Log::info("Push notification sent: " . $log->id);
        return true;
    }

    public function processPendingNotifications()
    {
        $pendingNotifications = NotificationLog::getPendingNotifications();
        
        foreach ($pendingNotifications as $notification) {
            try {
                $success = false;
                switch ($notification->type) {
                    case 'email':
                        $success = $this->emailService->send($notification);
                        break;
                    case 'sms':
                        $success = $this->smsService->send($notification);
                        break;
                    case 'push':
                        $success = $this->sendPushNotification($notification);
                        break;
                }

                if ($success) {
                    $notification->markAsSent();
                } else {
                    $notification->markAsFailed('Failed to process notification');
                }
            } catch (\Exception $e) {
                $notification->markAsFailed($e->getMessage());
                Log::error("Failed to process notification {$notification->id}: " . $e->getMessage());
            }
        }
    }

    public function retryFailedNotifications()
    {
        $failedNotifications = NotificationLog::getFailedNotifications();
        
        foreach ($failedNotifications as $notification) {
            try {
                $success = false;
                switch ($notification->type) {
                    case 'email':
                        $success = $this->emailService->send($notification);
                        break;
                    case 'sms':
                        $success = $this->smsService->send($notification);
                        break;
                    case 'push':
                        $success = $this->sendPushNotification($notification);
                        break;
                }

                if ($success) {
                    $notification->markAsSent();
                } else {
                    $notification->markAsFailed('Retry failed');
                }
            } catch (\Exception $e) {
                $notification->markAsFailed($e->getMessage());
                Log::error("Failed to retry notification {$notification->id}: " . $e->getMessage());
            }
        }
    }
}
