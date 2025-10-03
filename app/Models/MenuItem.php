<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
        'url',
        'route',
        'parent_id',
        'order',
        'icon',
        'target',
        'location',
        'is_active',
        'show_when_logged_in',
        'permissions',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_when_logged_in' => 'boolean',
        'permissions' => 'array',
    ];

    /**
     * Get the parent menu item
     */
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    /**
     * Get child menu items
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    /**
     * Get the URL for this menu item
     */
    public function getUrlAttribute($value)
    {
        if ($this->route) {
            return route($this->route);
        }
        return $value;
    }

    /**
     * Scope for active menu items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific location
     */
    public function scopeLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope for top-level menu items
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Check if menu item should be displayed based on auth status
     */
    public function shouldDisplay()
    {
        if ($this->show_when_logged_in === null) {
            return true;
        }

        if ($this->show_when_logged_in) {
            return auth()->check();
        }

        return !auth()->check();
    }
}
