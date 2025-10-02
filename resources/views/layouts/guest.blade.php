<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-pink-50 via-white to-purple-50">
            <!-- Logo Section -->
            <div class="mb-8">
                <a href="/" wire:navigate class="flex flex-col items-center">
                    <x-salon-logo class="w-16 h-16 fill-current text-pink-600 mb-2" />
                    @php
                        $salonName = \App\Models\SalonSetting::getDefault()->salon_name ?? 'Beauty Salon';
                    @endphp
                    <h1 class="text-2xl font-bold text-gray-900">{{ $salonName }}</h1>
                    <p class="text-sm text-gray-600">Management System</p>
                </a>
            </div>

            <!-- Main Content Card -->
            <div class="w-full sm:max-w-md">
                <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
                    <div class="px-8 py-8">
                        {{ $slot }}
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center mt-6">
                    <p class="text-xs text-gray-500">
                        © {{ date('Y') }} Beauty Salon Management System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
