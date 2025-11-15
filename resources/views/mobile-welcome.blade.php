<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>service business Management - Professional Salon Services</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/mobile.css', 'resources/js/app.js'])
    
    <!-- Mobile-specific meta tags -->
    <meta name="theme-color" content="#EC4899">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="service business">
</head>
<body class="antialiased font-sans bg-gray-50">
    <!-- Mobile Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-xl font-bold text-pink-600">service business</h1>
                </div>
                
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-pink-500" aria-controls="mobile-menu" aria-expanded="false">
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

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#services" class="text-gray-700 hover:text-pink-600 px-3 py-2 rounded-md text-sm font-medium">Services</a>
                    <a href="#about" class="text-gray-700 hover:text-pink-600 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-pink-600 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    <a href="{{ route('booking') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-md text-sm font-medium">Book Now</a>
                    <a href="{{ route('manage-booking') }}" class="text-gray-700 hover:text-pink-600 px-3 py-2 rounded-md text-sm font-medium">Manage Booking</a>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-600 px-3 py-2 rounded-md text-sm font-medium">Login</a>
                            <a href="{{ route('register') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-md text-sm font-medium">Register</a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white border-t border-gray-200">
                <a href="#services" class="text-gray-700 hover:bg-gray-50 hover:text-pink-600 block px-3 py-2 rounded-md text-base font-medium">Services</a>
                <a href="#about" class="text-gray-700 hover:bg-gray-50 hover:text-pink-600 block px-3 py-2 rounded-md text-base font-medium">About</a>
                <a href="#contact" class="text-gray-700 hover:bg-gray-50 hover:text-pink-600 block px-3 py-2 rounded-md text-base font-medium">Contact</a>
                <a href="{{ route('booking') }}" class="bg-pink-600 hover:bg-pink-700 text-white block px-3 py-2 rounded-md text-base font-medium">Book Now</a>
                <a href="{{ route('manage-booking') }}" class="text-gray-700 hover:bg-gray-50 hover:text-pink-600 block px-3 py-2 rounded-md text-base font-medium">Manage Booking</a>
                @if (Route::has('login'))
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-pink-600 hover:bg-pink-700 text-white block px-3 py-2 rounded-md text-base font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:bg-gray-50 hover:text-pink-600 block px-3 py-2 rounded-md text-base font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-pink-600 hover:bg-pink-700 text-white block px-3 py-2 rounded-md text-base font-medium">Register</a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Mobile Hero Section -->
    <section class="bg-gradient-to-r from-pink-100 to-purple-100 py-12 md:py-20">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-6xl font-bold text-gray-900 mb-4 md:mb-6">
                    Welcome to <span class="text-pink-600">service business</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Experience luxury beauty treatments in a relaxing environment. Book your appointment today and let us pamper you.
                </p>
                
                <!-- Mobile CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('booking') }}" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-4 rounded-lg font-semibold text-lg shadow-lg">
                        Book Appointment
                    </a>
                    <a href="{{ route('manage-booking') }}" class="bg-white hover:bg-gray-50 text-pink-600 px-8 py-4 rounded-lg font-semibold text-lg border-2 border-pink-600">
                        Manage Booking
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Services Section -->
    <section id="services" class="py-12 md:py-20 bg-white">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Services</h2>
                <p class="text-lg text-gray-600">Professional beauty treatments tailored to your needs</p>
            </div>

            <!-- Mobile Services Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Facial Treatments</h3>
                    <p class="text-gray-600 mb-4">Rejuvenating facials to restore your skin's natural glow and vitality.</p>
                    <div class="text-pink-600 font-semibold">From $80</div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Hair Styling</h3>
                    <p class="text-gray-600 mb-4">Professional hair cuts, coloring, and styling services for any occasion.</p>
                    <div class="text-pink-600 font-semibold">From $60</div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
                    <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Manicure & Pedicure</h3>
                    <p class="text-gray-600 mb-4">Complete nail care services to keep your hands and feet looking beautiful.</p>
                    <div class="text-pink-600 font-semibold">From $40</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Features Section -->
    <section class="py-12 md:py-20 bg-gray-50">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Why Choose Us</h2>
                <p class="text-lg text-gray-600">Experience the difference with our mobile-first booking system</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Easy Booking</h3>
                    <p class="text-gray-600">Book appointments in just a few taps with our mobile-optimized interface.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Instant Confirmation</h3>
                    <p class="text-gray-600">Get immediate confirmation and reminders for your appointments.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Expert Staff</h3>
                    <p class="text-gray-600">Professional beauticians with years of experience and training.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mobile Management</h3>
                    <p class="text-gray-600">Manage your appointments anytime, anywhere with our mobile app.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile CTA Section -->
    <section class="py-12 md:py-20 bg-pink-600">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Ready to Book?</h2>
                <p class="text-lg text-pink-100 mb-8 max-w-2xl mx-auto">
                    Experience our mobile-first booking system designed for your convenience. Book, manage, and reschedule appointments with ease.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('booking') }}" class="bg-white hover:bg-gray-100 text-pink-600 px-8 py-4 rounded-lg font-semibold text-lg shadow-lg">
                        Book Now
                    </a>
                    <a href="{{ route('manage-booking') }}" class="bg-pink-700 hover:bg-pink-800 text-white px-8 py-4 rounded-lg font-semibold text-lg border-2 border-pink-700">
                        Manage Booking
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mobile Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold text-pink-400 mb-4">service business</h3>
                    <p class="text-gray-300 mb-4">Professional beauty services with mobile-first convenience.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-pink-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-pink-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-pink-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('booking') }}" class="text-gray-300 hover:text-pink-400">Book Appointment</a></li>
                        <li><a href="{{ route('manage-booking') }}" class="text-gray-300 hover:text-pink-400">Manage Booking</a></li>
                        <li><a href="#services" class="text-gray-300 hover:text-pink-400">Services</a></li>
                        <li><a href="#about" class="text-gray-300 hover:text-pink-400">About Us</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Contact Info</h4>
                    <div class="space-y-2 text-gray-300">
                        <p>123 Beauty Street</p>
                        <p>City, State 12345</p>
                        <p>Phone: (555) 123-4567</p>
                        <p>Email: info@bookingflow.com</p>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; 2024 service business Management. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Menu JavaScript -->
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
                if (window.innerWidth >= 768 && isMenuOpen) {
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

