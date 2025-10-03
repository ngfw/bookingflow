<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ContactSubmission;
use App\Models\SalonSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class ContactForm extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $subject = '';
    public $message = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|min:10',
    ];

    protected $messages = [
        'name.required' => 'Please enter your name',
        'email.required' => 'Please enter your email address',
        'email.email' => 'Please enter a valid email address',
        'subject.required' => 'Please enter a subject',
        'message.required' => 'Please enter your message',
        'message.min' => 'Message must be at least 10 characters',
    ];

    public function submit()
    {
        $this->validate();

        // Create contact submission
        $submission = ContactSubmission::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'new',
        ]);

        // Send email notification to admin
        $this->sendEmailNotification($submission);

        // Reset form
        $this->reset(['name', 'email', 'phone', 'subject', 'message']);

        // Show success message
        session()->flash('success', 'Thank you for contacting us! We will get back to you soon.');
    }

    protected function sendEmailNotification($submission)
    {
        try {
            $settings = SalonSetting::getDefault();
            $adminEmail = $settings->salon_email ?? config('mail.from.address');

            if ($adminEmail) {
                Mail::send('emails.contact-submission', ['submission' => $submission], function ($message) use ($adminEmail, $submission) {
                    $message->to($adminEmail)
                            ->subject('New Contact Form Submission: ' . $submission->subject);
                    $message->replyTo($submission->email, $submission->name);
                });
            }
        } catch (\Exception $e) {
            \Log::error('Contact form email failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.contact-form');
    }
}
