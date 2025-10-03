<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/css/mobile.css', 'resources/js/app.js'])
        
        <!-- Mobile-specific meta tags -->
        <meta name="theme-color" content="#3B82F6">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="Beauty Salon Admin">
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <!-- Mobile-optimized Admin Navigation -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <!-- Mobile menu button -->
                        <div class="flex items-center lg:hidden">
                            <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
                                <span class="sr-only">Open main menu</span>
                                <!-- Hamburger icon -->
                                <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <!-- Close icon -->
                                <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                                <x-salon-logo class="h-8 w-auto mr-3" />
                                @php
                                    $salonName = \App\Models\SalonSetting::getDefault()->salon_name ?? 'Beauty Salon';
                                @endphp
                                <span class="hidden sm:inline text-lg lg:text-xl font-bold text-gray-900">{{ $salonName }} Admin</span>
                                <span class="sm:hidden text-lg font-bold text-gray-900">Admin</span>
                            </a>
                        </div>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden lg:flex lg:space-x-8">
                            <a href="{{ route('admin.dashboard') }}" class="border-indigo-400 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            @if(auth()->user()->hasPermission('view_clients'))
                                <a href="{{ route('admin.clients.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Clients
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('view_staff'))
                                <a href="{{ route('admin.staff.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Staff
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('view_appointments'))
                                <a href="{{ route('admin.appointments.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Appointments
                                </a>
                            @endif
                            @if(auth()->user()->hasPermission('use_pos'))
                                <a href="{{ route('admin.pos.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    POS
                                </a>
                            @endif
                            @if(auth()->user()->hasRole('super_admin'))
                                <a href="{{ route('admin.settings.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </a>
                            @endif
                            @if(auth()->user()->hasRole('super_admin'))
                                <a href="{{ route('admin.reports.dashboard') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                    Reports
                                </a>
                            @endif
                        </div>

                        <!-- Right side -->
                        <div class="flex items-center">
                            <!-- Mobile profile -->
                            <div class="lg:hidden flex items-center space-x-2">
                                <span class="text-xs text-gray-700">{{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">
                                        Logout
                                    </button>
                                </form>
                            </div>
                            
                            <!-- Desktop profile -->
                            <div class="hidden lg:flex lg:items-center lg:ml-6">
                                <span class="text-sm text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                                <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                    @csrf
                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="lg:hidden hidden" id="mobile-menu">
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-900 hover:bg-gray-50 block px-3 py-2 rounded-md text-base font-medium">
                            Dashboard
                        </a>
                        @if(auth()->user()->hasPermission('view_clients'))
                            <a href="{{ route('admin.clients.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Clients
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('view_staff'))
                            <a href="{{ route('admin.staff.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Staff
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('manage_staff_schedules'))
                            <a href="{{ route('admin.staff.schedule') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Schedule
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('view_staff_performance'))
                            <a href="{{ route('admin.staff.performance') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Performance
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('manage_staff_payroll'))
                            <a href="{{ route('admin.staff.commission') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Commission
                            </a>
                            <a href="{{ route('admin.staff.payroll') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Payroll
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('view_services'))
                            <a href="{{ route('admin.services.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Services
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('view_appointments'))
                            <a href="{{ route('admin.appointments.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Appointments
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('view_products'))
                            <a href="{{ route('admin.inventory.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Inventory
                            </a>
                        @endif
                        @if(auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('admin.reports.dashboard') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                Reports
                            </a>
                        @endif
                        @if(auth()->user()->hasPermission('use_pos'))
                            <a href="{{ route('admin.pos.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                POS
                            </a>
                        @endif
                        @if(auth()->user()->hasRole('super_admin'))
                            <a href="{{ route('admin.settings.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Settings
                                </div>
                            </a>
                        @endif
                        <a href="{{ route('admin.notifications.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Notifications
                        </a>
                        <a href="{{ route('admin.communication.history') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Communication
                        </a>
                        <a href="{{ route('admin.notifications.preferences') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Preferences
                        </a>
                        <a href="{{ route('admin.loyalty.points') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Loyalty
                        </a>
                        <a href="{{ route('admin.loyalty.rewards') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Rewards
                        </a>
                        <a href="{{ route('admin.referrals') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Referrals
                        </a>
                        <a href="{{ route('admin.membership.tiers') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Membership
                        </a>
                        <a href="{{ route('admin.specials.birthday-anniversary') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Specials
                        </a>
                        <a href="{{ route('admin.promotions.campaigns') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Campaigns
                        </a>
                        <a href="{{ route('admin.contact-submissions.index') }}" class="text-gray-500 hover:bg-gray-50 hover:text-gray-900 block px-3 py-2 rounded-md text-base font-medium">
                            Contact Messages
                        </a>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-4 lg:py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Mobile menu JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                let isMenuOpen = false;

                mobileMenuButton.addEventListener('click', function() {
                    isMenuOpen = !isMenuOpen;
                    
                    if (isMenuOpen) {
                        mobileMenu.classList.remove('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'true');
                        // Show close icon
                        mobileMenuButton.querySelector('svg:first-child').classList.add('hidden');
                        mobileMenuButton.querySelector('svg:last-child').classList.remove('hidden');
                    } else {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                        // Show hamburger icon
                        mobileMenuButton.querySelector('svg:first-child').classList.remove('hidden');
                        mobileMenuButton.querySelector('svg:last-child').classList.add('hidden');
                    }
                });

                // Close mobile menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (isMenuOpen && !mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                        isMenuOpen = false;
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                        mobileMenuButton.querySelector('svg:first-child').classList.remove('hidden');
                        mobileMenuButton.querySelector('svg:last-child').classList.add('hidden');
                    }
                });

                // Close mobile menu when window is resized to desktop
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 1024 && isMenuOpen) {
                        isMenuOpen = false;
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                        mobileMenuButton.querySelector('svg:first-child').classList.remove('hidden');
                        mobileMenuButton.querySelector('svg:last-child').classList.add('hidden');
                    }
                });
            });
        </script>
    </body>
</html>
