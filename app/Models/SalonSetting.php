<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalonSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'salon_name',
        'salon_description',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'contact_info',
        'social_links',
        'seo_settings',
        'homepage_settings',
        'booking_settings',
        'is_active',
    ];

    protected $casts = [
        'contact_info' => 'array',
        'social_links' => 'array',
        'seo_settings' => 'array',
        'homepage_settings' => 'array',
        'booking_settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the default salon settings
     */
    public static function getDefault()
    {
        return static::where('is_active', true)->first() ?? static::create([
            'salon_name' => 'Beauty Salon',
            'salon_description' => 'Experience luxury beauty services in a relaxing environment.',
            'primary_color' => '#ec4899',
            'secondary_color' => '#8b5cf6',
            'accent_color' => '#f59e0b',
            'font_family' => 'Inter',
            'contact_info' => [
                'phone' => '+1 (555) 123-4567',
                'email' => 'info@beautysalon.com',
                'address' => '123 Beauty Street, City, State 12345',
                'hours' => [
                    'monday' => '9:00 AM - 7:00 PM',
                    'tuesday' => '9:00 AM - 7:00 PM',
                    'wednesday' => '9:00 AM - 7:00 PM',
                    'thursday' => '9:00 AM - 7:00 PM',
                    'friday' => '9:00 AM - 8:00 PM',
                    'saturday' => '9:00 AM - 6:00 PM',
                    'sunday' => '10:00 AM - 5:00 PM',
                ],
            ],
            'social_links' => [
                'facebook' => null,
                'instagram' => null,
                'twitter' => null,
                'youtube' => null,
            ],
            'seo_settings' => [
                'meta_title' => 'Beauty Salon - Luxury Beauty Services',
                'meta_description' => 'Experience luxury beauty services in a relaxing environment. Professional team dedicated to making you look and feel your best.',
                'meta_keywords' => 'beauty salon, spa, hair, nails, massage, beauty services',
            ],
            'homepage_settings' => [
                'hero_title' => 'Welcome to Beauty Salon',
                'hero_subtitle' => 'Experience luxury beauty services in a relaxing environment.',
                'hero_button_text' => 'Book Appointment',
                'hero_button_link' => '/book',
                'featured_services' => true,
                'show_testimonials' => true,
                'show_gallery' => true,
            ],
            'booking_settings' => [
                'max_booking_days' => 14, // Default to 2 weeks
                'min_booking_hours' => 2, // Minimum hours in advance
                'allow_same_day_booking' => true,
                'booking_time_slots' => 30, // 30-minute intervals
                'enable_waitlist' => true,
                'require_payment_upfront' => false,
                'cancellation_deadline_hours' => 24,
            ],
        ]);
    }

    /**
     * Get CSS custom properties for the salon theme
     */
    public function getThemeCss()
    {
        return "
            :root {
                --primary-color: {$this->primary_color};
                --secondary-color: {$this->secondary_color};
                --accent-color: {$this->accent_color};
                --font-family: {$this->font_family};
            }
        ";
    }

    /**
     * Get the maximum booking days setting
     */
    public function getMaxBookingDays()
    {
        return $this->booking_settings['max_booking_days'] ?? 14;
    }

    /**
     * Get booking options for admin dropdown
     */
    public static function getBookingDaysOptions()
    {
        return [
            7 => '1 Week',
            14 => '2 Weeks',
            30 => '1 Month',
            60 => '2 Months',
            90 => '3 Months',
        ];
    }

    /**
     * Get the booking settings for the application
     */
    public function getBookingSettings()
    {
        return array_merge([
            'max_booking_days' => 14,
            'min_booking_hours' => 2,
            'allow_same_day_booking' => true,
            'booking_time_slots' => 30,
            'enable_waitlist' => true,
            'require_payment_upfront' => false,
            'cancellation_deadline_hours' => 24,
        ], $this->booking_settings ?? []);
    }
}
