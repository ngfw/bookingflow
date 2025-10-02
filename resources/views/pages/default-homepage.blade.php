<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Beauty Salon Management') }}</title>
    <meta name="description" content="Professional beauty salon services - book your appointment today">
    <meta name="keywords" content="beauty salon, hair, nails, spa, appointments">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <span class="text-2xl font-bold text-pink-600">{{ config('app.name', 'Beauty Salon') }}</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-6 md:flex md:space-x-8">
                        <a href="/" class="text-gray-900 hover:text-pink-600 px-3 py-2 text-sm font-medium">Home</a>
                        <a href="/services" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium">Services</a>
                        <a href="/gallery" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium">Gallery</a>
                        <a href="/blog" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium">Blog</a>
                        <a href="/contact" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium">Contact</a>
                    </div>
                </div>

                <div class="flex items-center">
                    <!-- Book Appointment Button -->
                    <a href="/book" class="ml-4 bg-pink-600 hover:bg-pink-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Book Appointment
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-pink-100 to-purple-100 py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                        Welcome to <span class="text-pink-600">{{ config('app.name', 'Beauty Salon') }}</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                        Professional beauty services tailored to make you look and feel your best. Book your appointment today!
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/book" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg text-lg font-medium">
                            Book Appointment
                        </a>
                        <a href="/services" class="bg-white hover:bg-gray-50 text-pink-600 px-8 py-3 rounded-lg text-lg font-medium border-2 border-pink-600">
                            View Services
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Services</h2>
                    <p class="text-lg text-gray-600">Professional beauty services tailored to your needs</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Sample Service Cards -->
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Hair Styling</h3>
                        <p class="text-gray-600 mb-4">Professional hair cuts, styling, and coloring services</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-pink-600">$50+</span>
                            <span class="text-sm text-gray-500">60 min</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Nail Care</h3>
                        <p class="text-gray-600 mb-4">Manicures, pedicures, and nail art services</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-pink-600">$30+</span>
                            <span class="text-sm text-gray-500">45 min</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Spa Treatments</h3>
                        <p class="text-gray-600 mb-4">Relaxing facials and skin care treatments</p>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-pink-600">$75+</span>
                            <span class="text-sm text-gray-500">90 min</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 bg-pink-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Ready to Book Your Appointment?</h2>
                <p class="text-lg text-gray-600 mb-8">Experience professional beauty services in a relaxing environment</p>
                <a href="/book" class="bg-pink-600 hover:bg-pink-700 text-white px-8 py-3 rounded-lg text-lg font-medium">
                    Book Now
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Salon Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ config('app.name', 'Beauty Salon') }}</h3>
                    <p class="text-gray-400 mb-4">Professional beauty services in a welcoming environment</p>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400 mb-2">Phone: (555) 123-4567</p>
                    <p class="text-gray-400 mb-2">Email: info@beautysalon.com</p>
                </div>

                <!-- Hours -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Hours</h3>
                    <p class="text-gray-400 text-sm">Mon-Fri: 9:00 AM - 7:00 PM</p>
                    <p class="text-gray-400 text-sm">Sat: 9:00 AM - 6:00 PM</p>
                    <p class="text-gray-400 text-sm">Sun: 10:00 AM - 5:00 PM</p>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} {{ config('app.name', 'Beauty Salon') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>