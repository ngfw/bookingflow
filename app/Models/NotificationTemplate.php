<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'event',
        'subject',
        'body',
        'variables',
        'is_active',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function logs()
    {
        return $this->hasMany(NotificationLog::class, 'template_id');
    }

    public function renderTemplate(array $data = [])
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($data as $key => $value) {
            $subject = str_replace("{{$key}}", $value, $subject);
            $body = str_replace("{{$key}}", $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    public static function getTemplate($type, $event)
    {
        return self::where('type', $type)
            ->where('event', $event)
            ->where('is_active', true)
            ->first();
    }

    public static function getDefaultTemplate($type, $event)
    {
        return self::where('type', $type)
            ->where('event', $event)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }

    public static function createDefaultTemplates()
    {
        $templates = [
            // Email Templates
            [
                'name' => 'Appointment Confirmation Email',
                'type' => 'email',
                'event' => 'appointment_confirmation',
                'subject' => 'Appointment Confirmed - {{salon_name}}',
                'body' => "Dear {{client_name}},\n\nYour appointment has been confirmed:\n\nDate: {{appointment_date}}\nTime: {{appointment_time}}\nService: {{service_name}}\nStaff: {{staff_name}}\n\nPlease arrive 10 minutes early.\n\nThank you for choosing {{salon_name}}!\n\nBest regards,\n{{salon_name}} Team",
                'variables' => ['client_name', 'appointment_date', 'appointment_time', 'service_name', 'staff_name', 'salon_name'],
                'is_default' => true,
            ],
            [
                'name' => 'Appointment Reminder Email',
                'type' => 'email',
                'event' => 'appointment_reminder',
                'subject' => 'Reminder: Your appointment tomorrow - {{salon_name}}',
                'body' => "Dear {{client_name}},\n\nThis is a reminder for your appointment:\n\nDate: {{appointment_date}}\nTime: {{appointment_time}}\nService: {{service_name}}\nStaff: {{staff_name}}\n\nPlease arrive 10 minutes early.\n\nSee you soon!\n\n{{salon_name}} Team",
                'variables' => ['client_name', 'appointment_date', 'appointment_time', 'service_name', 'staff_name', 'salon_name'],
                'is_default' => true,
            ],
            [
                'name' => 'Payment Receipt Email',
                'type' => 'email',
                'event' => 'payment_receipt',
                'subject' => 'Payment Receipt - {{invoice_number}}',
                'body' => "Dear {{client_name}},\n\nThank you for your payment!\n\nInvoice: {{invoice_number}}\nAmount: \${{amount}}\nPayment Method: {{payment_method}}\nDate: {{payment_date}}\n\nServices:\n{{services_list}}\n\nThank you for your business!\n\n{{salon_name}} Team",
                'variables' => ['client_name', 'invoice_number', 'amount', 'payment_method', 'payment_date', 'services_list', 'salon_name'],
                'is_default' => true,
            ],
            [
                'name' => 'Appointment Cancellation Email',
                'type' => 'email',
                'event' => 'appointment_cancellation',
                'subject' => 'Appointment Cancelled - {{salon_name}}',
                'body' => "Dear {{client_name}},\n\nYour appointment has been cancelled:\n\nDate: {{appointment_date}}\nTime: {{appointment_time}}\nService: {{service_name}}\nStaff: {{staff_name}}\n\nReason: {{cancellation_reason}}\n\nWe hope to see you again soon!\n\n{{salon_name}} Team",
                'variables' => ['client_name', 'appointment_date', 'appointment_time', 'service_name', 'staff_name', 'cancellation_reason', 'salon_name'],
                'is_default' => true,
            ],
            [
                'name' => 'Welcome Email',
                'type' => 'email',
                'event' => 'client_welcome',
                'subject' => 'Welcome to {{salon_name}}!',
                'body' => "Dear {{client_name}},\n\nWelcome to {{salon_name}}!\n\nWe're excited to have you as our client. Here's what you can expect:\n\n- Professional beauty services\n- Expert staff members\n- Comfortable and clean environment\n- Flexible scheduling\n\nBook your first appointment today!\n\nBest regards,\n{{salon_name}} Team",
                'variables' => ['client_name', 'salon_name'],
                'is_default' => true,
            ],
            
            // SMS Templates
            [
                'name' => 'Appointment Confirmation SMS',
                'type' => 'sms',
                'event' => 'appointment_confirmation',
                'subject' => null,
                'body' => "Hi {{client_name}}! Your appointment is confirmed for {{appointment_date}} at {{appointment_time}} with {{staff_name}}. Service: {{service_name}}. Please arrive 10 mins early. - {{salon_name}}",
                'variables' => ['client_name', 'appointment_date', 'appointment_time', 'service_name', 'staff_name', 'salon_name'],
                'is_default' => true,
            ],
            [
                'name' => 'Appointment Reminder SMS',
                'type' => 'sms',
                'event' => 'appointment_reminder',
                'subject' => null,
                'body' => "Reminder: You have an appointment tomorrow at {{appointment_time}} with {{staff_name}} for {{service_name}}. Please arrive 10 mins early. - {{salon_name}}",
                'variables' => ['appointment_time', 'service_name', 'staff_name', 'salon_name'],
                'is_default' => true,
            ],
            [
                'name' => 'Payment Receipt SMS',
                'type' => 'sms',
                'event' => 'payment_receipt',
                'subject' => null,
                'body' => "Payment received! Invoice {{invoice_number}}: \${{amount}} via {{payment_method}} on {{payment_date}}. Thank you! - {{salon_name}}",
                'variables' => ['invoice_number', 'amount', 'payment_method', 'payment_date', 'salon_name'],
                'is_default' => true,
            ],
        ];

        foreach ($templates as $template) {
            self::firstOrCreate(
                ['type' => $template['type'], 'event' => $template['event']],
                $template
            );
        }
    }
}