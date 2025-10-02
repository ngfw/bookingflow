<?php

namespace App\Livewire\Admin;

use App\Models\SalonSetting;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class Settings extends Component
{
    public $settings;
    public $booking_settings = [];
    
    // Form properties
    public $salon_name;
    public $salon_description;
    public $salon_phone;
    public $salon_email;
    public $salon_address;
    public $salon_city;
    public $salon_state;
    public $salon_zip;
    public $salon_country;
    public $salon_latitude;
    public $salon_longitude;
    public $service_radius;
    public $service_radius_unit = 'miles';
    public $enable_location_restriction = false;
    public $location_verification_message;
    public $salon_website;
    public $primary_color;
    public $secondary_color;
    public $accent_color;
    public $max_booking_days;
    public $min_booking_hours;
    public $allow_same_day_booking;
    public $booking_time_slots;
    public $enable_waitlist;
    public $require_payment_upfront;
    public $cancellation_deadline_hours;
    
    // Google Analytics
    public $google_analytics_id;
    public $google_analytics_enabled;
    
    // Google reCAPTCHA
    public $recaptcha_site_key;
    public $recaptcha_secret_key;
    public $recaptcha_enabled = false;
    
    // Translations & Localization
    public $default_language;
    public $available_languages = [];
    public $enable_multi_language;
    public $timezone;
    public $date_format;
    public $time_format;
    public $currency;
    public $currency_symbol;
    
    // SEO Settings
    public $meta_title;
    public $meta_description;
    public $meta_keywords;
    
    // Social Media
    public $facebook_url;
    public $instagram_url;
    public $twitter_url;
    public $youtube_url;
    
    // Notification Settings
    public $email_notifications_enabled;
    public $sms_notifications_enabled;
    public $notification_email;
    
    // Business Hours
    public $business_hours = [];
    
    // Terms & Privacy
    public $terms_of_service;
    public $privacy_policy;

    protected $rules = [
        'salon_name' => 'required|string|max:255',
        'salon_description' => 'nullable|string',
        'salon_phone' => 'nullable|string|max:20',
        'salon_email' => 'nullable|email|max:255',
        'salon_address' => 'nullable|string|max:255',
        'salon_city' => 'nullable|string|max:100',
        'salon_state' => 'nullable|string|max:100',
        'salon_zip' => 'nullable|string|max:20',
        'salon_country' => 'nullable|string|max:100',
        'salon_latitude' => 'nullable|numeric|between:-90,90',
        'salon_longitude' => 'nullable|numeric|between:-180,180',
        'service_radius' => 'nullable|numeric|min:1|max:500',
        'service_radius_unit' => 'required|in:miles,kilometers',
        'enable_location_restriction' => 'boolean',
        'location_verification_message' => 'nullable|string',
        'salon_website' => 'nullable|url|max:255',
        'primary_color' => 'required|string|size:7',
        'secondary_color' => 'required|string|size:7',
        'accent_color' => 'required|string|size:7',
        'max_booking_days' => 'required|integer|min:1|max:365',
        'min_booking_hours' => 'required|integer|min:0|max:168',
        'allow_same_day_booking' => 'boolean',
        'booking_time_slots' => 'required|integer|in:15,30,60',
        'enable_waitlist' => 'boolean',
        'require_payment_upfront' => 'boolean',
        'cancellation_deadline_hours' => 'required|integer|min:0|max:168',
        'google_analytics_id' => 'nullable|string|max:50',
        'google_analytics_enabled' => 'boolean',
        'recaptcha_site_key' => 'nullable|string|max:255',
        'recaptcha_secret_key' => 'nullable|string|max:255',
        'recaptcha_enabled' => 'boolean',
        'default_language' => 'required|string|max:10',
        'enable_multi_language' => 'boolean',
        'timezone' => 'required|string|max:50',
        'date_format' => 'required|string|max:20',
        'time_format' => 'required|string|max:20',
        'currency' => 'required|string|max:10',
        'currency_symbol' => 'required|string|max:5',
        'meta_title' => 'nullable|string|max:255',
        'meta_description' => 'nullable|string|max:500',
        'meta_keywords' => 'nullable|string|max:500',
        'facebook_url' => 'nullable|url|max:255',
        'instagram_url' => 'nullable|url|max:255',
        'twitter_url' => 'nullable|url|max:255',
        'youtube_url' => 'nullable|url|max:255',
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'notification_email' => 'nullable|email|max:255',
        'terms_of_service' => 'nullable|string',
        'privacy_policy' => 'nullable|string',
    ];

    public function mount()
    {
        $this->settings = SalonSetting::getDefault();
        $this->initializeDefaults();
        $this->loadSettings();
    }

    private function initializeDefaults()
    {
        $this->available_languages = [
            'en' => 'English',
            'es' => 'Spanish', 
            'fr' => 'French',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ja' => 'Japanese',
            'ko' => 'Korean',
            'zh' => 'Chinese',
            'ar' => 'Arabic'
        ];
        
        $this->business_hours = [
            'monday' => ['open' => '09:00', 'close' => '17:00', 'enabled' => true],
            'tuesday' => ['open' => '09:00', 'close' => '17:00', 'enabled' => true],
            'wednesday' => ['open' => '09:00', 'close' => '17:00', 'enabled' => true],
            'thursday' => ['open' => '09:00', 'close' => '17:00', 'enabled' => true],
            'friday' => ['open' => '09:00', 'close' => '17:00', 'enabled' => true],
            'saturday' => ['open' => '10:00', 'close' => '16:00', 'enabled' => true],
            'sunday' => ['open' => '10:00', 'close' => '16:00', 'enabled' => false],
        ];
    }

    public function loadSettings()
    {
        // Basic Information
        $this->salon_name = $this->settings->salon_name;
        $this->salon_description = $this->settings->salon_description;
        $this->salon_phone = $this->settings->salon_phone ?? '';
        $this->salon_email = $this->settings->salon_email ?? '';
        $this->salon_address = $this->settings->salon_address ?? '';
        $this->salon_city = $this->settings->salon_city ?? '';
        $this->salon_state = $this->settings->salon_state ?? '';
        $this->salon_zip = $this->settings->salon_zip ?? '';
        $this->salon_country = $this->settings->salon_country ?? 'United States';
        $this->salon_latitude = $this->settings->salon_latitude ?? '';
        $this->salon_longitude = $this->settings->salon_longitude ?? '';
        $this->service_radius = $this->settings->service_radius ?? 25;
        $this->service_radius_unit = $this->settings->service_radius_unit ?? 'miles';
        $this->enable_location_restriction = $this->settings->enable_location_restriction ?? false;
        $this->location_verification_message = $this->settings->location_verification_message ?? 'Please confirm you are located within our service area before booking.';
        $this->salon_website = $this->settings->salon_website ?? '';
        
        // Theme Colors
        $this->primary_color = $this->settings->primary_color;
        $this->secondary_color = $this->settings->secondary_color;
        $this->accent_color = $this->settings->accent_color;

        // Booking Settings
        $bookingSettings = $this->settings->getBookingSettings();
        $this->max_booking_days = $bookingSettings['max_booking_days'];
        $this->min_booking_hours = $bookingSettings['min_booking_hours'];
        $this->allow_same_day_booking = $bookingSettings['allow_same_day_booking'];
        $this->booking_time_slots = $bookingSettings['booking_time_slots'];
        $this->enable_waitlist = $bookingSettings['enable_waitlist'];
        $this->require_payment_upfront = $bookingSettings['require_payment_upfront'];
        $this->cancellation_deadline_hours = $bookingSettings['cancellation_deadline_hours'];
        
        // Google Analytics
        $this->google_analytics_id = $this->settings->google_analytics_id ?? '';
        $this->google_analytics_enabled = $this->settings->google_analytics_enabled ?? false;
        
        // Google reCAPTCHA
        $this->recaptcha_site_key = $this->settings->recaptcha_site_key ?? '';
        $this->recaptcha_secret_key = $this->settings->recaptcha_secret_key ?? '';
        $this->recaptcha_enabled = $this->settings->recaptcha_enabled ?? false;
        
        // Localization
        $this->default_language = $this->settings->default_language ?? 'en';
        $this->enable_multi_language = $this->settings->enable_multi_language ?? false;
        $this->timezone = $this->settings->timezone ?? 'UTC';
        $this->date_format = $this->settings->date_format ?? 'M d, Y';
        $this->time_format = $this->settings->time_format ?? 'g:i A';
        $this->currency = $this->settings->currency ?? 'USD';
        $this->currency_symbol = $this->settings->currency_symbol ?? '$';
        
        // SEO
        $this->meta_title = $this->settings->meta_title ?? '';
        $this->meta_description = $this->settings->meta_description ?? '';
        $this->meta_keywords = $this->settings->meta_keywords ?? '';
        
        // Social Media
        $this->facebook_url = $this->settings->facebook_url ?? '';
        $this->instagram_url = $this->settings->instagram_url ?? '';
        $this->twitter_url = $this->settings->twitter_url ?? '';
        $this->youtube_url = $this->settings->youtube_url ?? '';
        
        // Notifications
        $this->email_notifications_enabled = $this->settings->email_notifications_enabled ?? true;
        $this->sms_notifications_enabled = $this->settings->sms_notifications_enabled ?? false;
        $this->notification_email = $this->settings->notification_email ?? '';
        
        // Business Hours
        if ($this->settings->business_hours) {
            $this->business_hours = $this->settings->business_hours;
        }
        
        // Terms & Privacy
        $this->terms_of_service = $this->settings->terms_of_service ?? '';
        $this->privacy_policy = $this->settings->privacy_policy ?? '';
    }

    public function geocodeAddress()
    {
        if (!$this->salon_address || !$this->salon_city || !$this->salon_state) {
            session()->flash('error', 'Please fill in address, city, and state to get coordinates.');
            return;
        }

        $fullAddress = $this->salon_address . ', ' . $this->salon_city . ', ' . $this->salon_state . ', ' . $this->salon_zip;
        
        // Using OpenStreetMap Nominatim API (free alternative to Google Maps API)
        $encodedAddress = urlencode($fullAddress);
        $url = "https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=1&q={$encodedAddress}";
        
        try {
            $response = file_get_contents($url, false, stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Salon Booking System'
                ]
            ]));
            
            if ($response === false) {
                session()->flash('error', 'Unable to connect to geocoding service.');
                return;
            }
            
            $data = json_decode($response, true);
            
            if (!empty($data)) {
                $this->salon_latitude = round($data[0]['lat'], 6);
                $this->salon_longitude = round($data[0]['lon'], 6);
                session()->flash('success', 'Coordinates found! Latitude: ' . $this->salon_latitude . ', Longitude: ' . $this->salon_longitude);
            } else {
                session()->flash('error', 'Address not found. Please check the address details.');
            }
        } catch (Exception $e) {
            session()->flash('error', 'Error getting coordinates: ' . $e->getMessage());
        }
    }

    public function saveSettings()
    {
        $this->validate();

        $this->settings->update([
            // Basic Information
            'salon_name' => $this->salon_name,
            'salon_description' => $this->salon_description,
            'salon_phone' => $this->salon_phone,
            'salon_email' => $this->salon_email,
            'salon_address' => $this->salon_address,
            'salon_city' => $this->salon_city,
            'salon_state' => $this->salon_state,
            'salon_zip' => $this->salon_zip,
            'salon_country' => $this->salon_country,
            'salon_latitude' => $this->salon_latitude,
            'salon_longitude' => $this->salon_longitude,
            'service_radius' => $this->service_radius,
            'service_radius_unit' => $this->service_radius_unit,
            'enable_location_restriction' => $this->enable_location_restriction,
            'location_verification_message' => $this->location_verification_message,
            'salon_website' => $this->salon_website,
            
            // Theme Colors
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'accent_color' => $this->accent_color,
            
            // Google Analytics
            'google_analytics_id' => $this->google_analytics_id,
            'google_analytics_enabled' => $this->google_analytics_enabled,
            
            // Google reCAPTCHA
            'recaptcha_site_key' => $this->recaptcha_site_key,
            'recaptcha_secret_key' => $this->recaptcha_secret_key,
            'recaptcha_enabled' => $this->recaptcha_enabled,
            
            // Localization
            'default_language' => $this->default_language,
            'enable_multi_language' => $this->enable_multi_language,
            'timezone' => $this->timezone,
            'date_format' => $this->date_format,
            'time_format' => $this->time_format,
            'currency' => $this->currency,
            'currency_symbol' => $this->currency_symbol,
            
            // SEO
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            
            // Social Media
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'twitter_url' => $this->twitter_url,
            'youtube_url' => $this->youtube_url,
            
            // Notifications
            'email_notifications_enabled' => $this->email_notifications_enabled,
            'sms_notifications_enabled' => $this->sms_notifications_enabled,
            'notification_email' => $this->notification_email,
            
            // Business Hours
            'business_hours' => $this->business_hours,
            
            // Terms & Privacy
            'terms_of_service' => $this->terms_of_service,
            'privacy_policy' => $this->privacy_policy,
            
            // Booking Settings
            'booking_settings' => [
                'max_booking_days' => $this->max_booking_days,
                'min_booking_hours' => $this->min_booking_hours,
                'allow_same_day_booking' => $this->allow_same_day_booking,
                'booking_time_slots' => $this->booking_time_slots,
                'enable_waitlist' => $this->enable_waitlist,
                'require_payment_upfront' => $this->require_payment_upfront,
                'cancellation_deadline_hours' => $this->cancellation_deadline_hours,
            ],
        ]);

        session()->flash('message', 'Settings updated successfully!');
    }

    public function getTimezones()
    {
        return [
            'UTC' => 'UTC',
            'America/New_York' => 'Eastern Time (ET)',
            'America/Chicago' => 'Central Time (CT)',
            'America/Denver' => 'Mountain Time (MT)',
            'America/Los_Angeles' => 'Pacific Time (PT)',
            'Europe/London' => 'London (GMT)',
            'Europe/Paris' => 'Paris (CET)',
            'Europe/Berlin' => 'Berlin (CET)',
            'Asia/Tokyo' => 'Tokyo (JST)',
            'Asia/Shanghai' => 'Shanghai (CST)',
            'Australia/Sydney' => 'Sydney (AEST)',
        ];
    }

    public function getCurrencies()
    {
        return [
            'USD' => ['symbol' => '$', 'name' => 'US Dollar'],
            'EUR' => ['symbol' => '€', 'name' => 'Euro'],
            'GBP' => ['symbol' => '£', 'name' => 'British Pound'],
            'CAD' => ['symbol' => 'C$', 'name' => 'Canadian Dollar'],
            'AUD' => ['symbol' => 'A$', 'name' => 'Australian Dollar'],
            'JPY' => ['symbol' => '¥', 'name' => 'Japanese Yen'],
        ];
    }

    public function getBookingDaysOptions()
    {
        return SalonSetting::getBookingDaysOptions();
    }

    public function render()
    {
        return view('livewire.admin.settings', [
            'bookingDaysOptions' => $this->getBookingDaysOptions(),
            'timezones' => $this->getTimezones(),
            'currencies' => $this->getCurrencies(),
        ]);
    }
}