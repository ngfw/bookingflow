<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Header Menu Items
        $menuItems = [
            [
                'label' => 'Home',
                'route' => 'home',
                'order' => 1,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'About',
                'route' => 'about',
                'order' => 2,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'Services',
                'route' => 'services',
                'order' => 3,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'Gallery',
                'route' => 'gallery',
                'order' => 4,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'Blog',
                'route' => 'blog',
                'order' => 5,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'Contact',
                'route' => 'contact',
                'order' => 6,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'Book Now',
                'route' => 'booking',
                'order' => 7,
                'location' => 'header',
                'is_active' => true,
            ],
            [
                'label' => 'Dashboard',
                'route' => 'dashboard',
                'order' => 8,
                'location' => 'header',
                'is_active' => true,
                'show_when_logged_in' => true,
            ],
            [
                'label' => 'Login',
                'route' => 'login',
                'order' => 9,
                'location' => 'header',
                'is_active' => true,
                'show_when_logged_in' => false,
            ],
            [
                'label' => 'Register',
                'route' => 'register',
                'order' => 10,
                'location' => 'header',
                'is_active' => true,
                'show_when_logged_in' => false,
            ],
        ];

        // Mobile Menu Items (same as header)
        $mobileItems = collect($menuItems)->map(function ($item) {
            $item['location'] = 'mobile';
            return $item;
        })->toArray();

        // Footer Menu Items
        $footerItems = [
            [
                'label' => 'Home',
                'route' => 'home',
                'order' => 1,
                'location' => 'footer',
                'is_active' => true,
            ],
            [
                'label' => 'About',
                'route' => 'about',
                'order' => 2,
                'location' => 'footer',
                'is_active' => true,
            ],
            [
                'label' => 'Services',
                'route' => 'services',
                'order' => 3,
                'location' => 'footer',
                'is_active' => true,
            ],
            [
                'label' => 'Gallery',
                'route' => 'gallery',
                'order' => 4,
                'location' => 'footer',
                'is_active' => true,
            ],
            [
                'label' => 'Contact',
                'route' => 'contact',
                'order' => 5,
                'location' => 'footer',
                'is_active' => true,
            ],
            [
                'label' => 'Book Now',
                'route' => 'booking',
                'order' => 6,
                'location' => 'footer',
                'is_active' => true,
            ],
        ];

        // Insert all menu items
        foreach (array_merge($menuItems, $mobileItems, $footerItems) as $item) {
            MenuItem::create($item);
        }
    }
}
