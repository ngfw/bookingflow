<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function send(NotificationLog $notification)
    {
        try {
            $recipient = $this->getRecipient($notification);
            
            if (!$recipient || !$recipient->email) {
                $notification->markAsFailed('No email address found');
                return false;
            }

            Mail::raw($notification->body, function ($message) use ($notification, $recipient) {
                $message->to($recipient->email)
                        ->subject($notification->subject);
            });

            Log::info("Email sent successfully to {$recipient->email}");
            return true;

        } catch (\Exception $e) {
            Log::error("Email sending failed: " . $e->getMessage());
            $notification->markAsFailed($e->getMessage());
            return false;
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
            
            Mail::raw($rendered['body'], function ($message) use ($rendered, $recipient) {
                $message->to($recipient->email)
                        ->subject($rendered['subject']);
            });

            return true;

        } catch (\Exception $e) {
            Log::error("Template email sending failed: " . $e->getMessage());
            return false;
        }
    }
}
