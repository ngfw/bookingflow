<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_name',
        'client_email',
        'client_phone',
        'service_id',
        'rating',
        'title',
        'content',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the service that the testimonial is for
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get featured testimonials
     */
    public static function getFeatured()
    {
        return static::where('is_featured', true)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get active testimonials
     */
    public static function getActive()
    {
        return static::where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get testimonials by rating
     */
    public static function getByRating($rating)
    {
        return static::where('rating', $rating)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();
    }

    /**
     * Get average rating
     */
    public static function getAverageRating()
    {
        return static::where('is_active', true)
                    ->avg('rating');
    }

    /**
     * Get rating distribution
     */
    public static function getRatingDistribution()
    {
        return static::where('is_active', true)
                    ->selectRaw('rating, COUNT(*) as count')
                    ->groupBy('rating')
                    ->orderBy('rating', 'desc')
                    ->get();
    }

    /**
     * Get the star rating HTML
     */
    public function getStarRatingAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
            } else {
                $stars .= '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
            }
        }
        return $stars;
    }

    /**
     * Get the client initials
     */
    public function getClientInitialsAttribute()
    {
        $name = $this->client_name;
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }
}
