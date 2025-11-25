<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BookingFlow') }} - Professional Booking Management</title>
    <meta name="description" content="Professional booking and appointment management system for service businesses. Book your appointment today!">
    <meta name="keywords" content="booking, appointments, salon, spa, services, scheduling">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Animation keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .animate-delay-100 { animation-delay: 0.1s; }
        .animate-delay-200 { animation-delay: 0.2s; }
        .animate-delay-300 { animation-delay: 0.3s; }
        .animate-delay-400 { animation-delay: 0.4s; }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Feature icon animation */
        .feature-icon {
            transition: all 0.3s ease;
        }
        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        }
        .feature-card:hover .feature-icon svg {
            color: white;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 lg:h-20">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <span class="text-xl lg:text-2xl font-bold gradient-text">{{ config('app.name', 'BookingFlow') }}</span>
                    </div>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden lg:ml-10 lg:flex lg:space-x-8">
                        <a href="/" class="text-gray-900 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-pink-600">Home</a>
                        <a href="/services" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Services</a>
                        <a href="/gallery" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Gallery</a>
                        <a href="/blog" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Blog</a>
                        <a href="/contact" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Contact</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Contact Number (Desktop) -->
                    <a href="tel:+15551234567" class="hidden lg:flex items-center text-gray-600 hover:text-pink-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-sm font-medium">(555) 123-4567</span>
                    </a>

                    <!-- Book Appointment Button -->
                    <a href="/book" class="bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white px-4 py-2 lg:px-6 lg:py-2.5 rounded-full text-sm font-medium shadow-lg shadow-pink-500/25 hover:shadow-pink-500/40 transition-all">
                        Book Now
                    </a>

                    <!-- Mobile Menu Button -->
                    <button type="button" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100" x-data="{ open: false }" @click="open = !open">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        <!-- ========================================
             HERO SECTION - Mobile First, Desktop Enhanced
             ======================================== -->
        <section class="relative overflow-hidden bg-gradient-to-br from-pink-50 via-white to-purple-50">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-40">
                <div class="absolute top-0 right-0 w-72 h-72 lg:w-96 lg:h-96 bg-pink-200 rounded-full filter blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-72 h-72 lg:w-96 lg:h-96 bg-purple-200 rounded-full filter blur-3xl"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24">
                <div class="lg:grid lg:grid-cols-2 lg:gap-12 lg:items-center">
                    <!-- Text Content -->
                    <div class="text-center lg:text-left mb-10 lg:mb-0">
                        <span class="inline-block px-4 py-1.5 bg-pink-100 text-pink-700 text-sm font-medium rounded-full mb-4 animate-fade-in-up">
                            ✨ Welcome to Excellence
                        </span>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-900 mb-6 animate-fade-in-up animate-delay-100">
                            Your Beauty,<br>
                            <span class="gradient-text">Our Passion</span>
                        </h1>
                        <p class="text-base sm:text-lg lg:text-xl text-gray-600 mb-8 max-w-xl mx-auto lg:mx-0 animate-fade-in-up animate-delay-200">
                            Experience premium beauty services tailored to make you look and feel your absolute best. Book your transformation today.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-fade-in-up animate-delay-300">
                            <a href="/book" class="inline-flex items-center justify-center bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white px-8 py-3.5 rounded-full text-base font-semibold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Book Appointment
                            </a>
                            <a href="/services" class="inline-flex items-center justify-center bg-white hover:bg-gray-50 text-gray-800 px-8 py-3.5 rounded-full text-base font-semibold border-2 border-gray-200 hover:border-pink-300 transition-all">
                                <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                View Services
                            </a>
                        </div>

                        <!-- Trust Indicators -->
                        <div class="mt-8 pt-8 border-t border-gray-200 animate-fade-in-up animate-delay-400">
                            <div class="flex flex-wrap items-center justify-center lg:justify-start gap-6">
                                <div class="flex items-center">
                                    <div class="flex -space-x-2">
                                        <div class="w-8 h-8 rounded-full bg-pink-200 border-2 border-white flex items-center justify-center text-xs font-medium text-pink-700">A</div>
                                        <div class="w-8 h-8 rounded-full bg-purple-200 border-2 border-white flex items-center justify-center text-xs font-medium text-purple-700">B</div>
                                        <div class="w-8 h-8 rounded-full bg-indigo-200 border-2 border-white flex items-center justify-center text-xs font-medium text-indigo-700">C</div>
                                    </div>
                                    <span class="ml-3 text-sm text-gray-600">500+ Happy Clients</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for($i = 0; $i < 5; $i++)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">4.9/5 Rating</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Image / Booking Preview (Desktop) -->
                    <div class="relative hidden lg:block">
                        <div class="relative bg-white rounded-2xl shadow-2xl p-6 transform rotate-1 hover:rotate-0 transition-transform duration-300">
                            <!-- Quick Booking Card -->
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Quick Booking</h3>
                                <p class="text-sm text-gray-500">Select a service to get started</p>
                            </div>
                            <div class="space-y-3">
                                <!-- Service Category Items -->
                                <a href="/book" class="flex items-center p-3 bg-gray-50 hover:bg-pink-50 rounded-xl transition-colors group">
                                    <div class="w-12 h-12 bg-gradient-to-br from-pink-100 to-purple-100 rounded-lg flex items-center justify-center mr-4 group-hover:from-pink-200 group-hover:to-purple-200 transition-colors">
                                        <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900 group-hover:text-pink-700">Hair Styling</span>
                                        <p class="text-xs text-gray-500">12 services</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                                <a href="/book" class="flex items-center p-3 bg-gray-50 hover:bg-pink-50 rounded-xl transition-colors group">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-lg flex items-center justify-center mr-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-colors">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900 group-hover:text-pink-700">Nail Care</span>
                                        <p class="text-xs text-gray-500">8 services</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                                <a href="/book" class="flex items-center p-3 bg-gray-50 hover:bg-pink-50 rounded-xl transition-colors group">
                                    <div class="w-12 h-12 bg-gradient-to-br from-rose-100 to-pink-100 rounded-lg flex items-center justify-center mr-4 group-hover:from-rose-200 group-hover:to-pink-200 transition-colors">
                                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                    </div>
                                    <div class="flex-1">
                                        <span class="font-medium text-gray-900 group-hover:text-pink-700">Spa & Wellness</span>
                                        <p class="text-xs text-gray-500">15 services</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-pink-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                            <a href="/book" class="mt-4 w-full inline-flex items-center justify-center bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-3 rounded-xl text-sm font-semibold hover:from-pink-700 hover:to-purple-700 transition-all">
                                View All Services
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                        <!-- Decorative Elements -->
                        <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-full opacity-60 blur-xl"></div>
                        <div class="absolute -bottom-4 -left-4 w-32 h-32 bg-gradient-to-br from-pink-200 to-purple-200 rounded-full opacity-60 blur-xl"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             FEATURES / WHY CHOOSE US SECTION
             ======================================== -->
        <section class="py-12 lg:py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 lg:mb-16">
                    <span class="inline-block px-4 py-1.5 bg-purple-100 text-purple-700 text-sm font-medium rounded-full mb-4">
                        Why Choose Us
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        Experience the <span class="gradient-text">Difference</span>
                    </h2>
                    <p class="text-base lg:text-lg text-gray-600 max-w-2xl mx-auto">
                        We combine expertise, premium products, and personalized care to deliver exceptional results every time.
                    </p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-8">
                    <!-- Feature 1 -->
                    <div class="feature-card bg-gray-50 hover:bg-white rounded-2xl p-4 lg:p-6 text-center card-hover border border-transparent hover:border-pink-100">
                        <div class="feature-icon w-12 h-12 lg:w-16 lg:h-16 bg-pink-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm lg:text-lg font-semibold text-gray-900 mb-2">Online Booking</h3>
                        <p class="text-xs lg:text-sm text-gray-600">Book appointments 24/7 from anywhere</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="feature-card bg-gray-50 hover:bg-white rounded-2xl p-4 lg:p-6 text-center card-hover border border-transparent hover:border-pink-100">
                        <div class="feature-icon w-12 h-12 lg:w-16 lg:h-16 bg-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </div>
                        <h3 class="text-sm lg:text-lg font-semibold text-gray-900 mb-2">Expert Stylists</h3>
                        <p class="text-xs lg:text-sm text-gray-600">Certified professionals with years of experience</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="feature-card bg-gray-50 hover:bg-white rounded-2xl p-4 lg:p-6 text-center card-hover border border-transparent hover:border-pink-100">
                        <div class="feature-icon w-12 h-12 lg:w-16 lg:h-16 bg-indigo-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <h3 class="text-sm lg:text-lg font-semibold text-gray-900 mb-2">Premium Products</h3>
                        <p class="text-xs lg:text-sm text-gray-600">Only the finest quality products used</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="feature-card bg-gray-50 hover:bg-white rounded-2xl p-4 lg:p-6 text-center card-hover border border-transparent hover:border-pink-100">
                        <div class="feature-icon w-12 h-12 lg:w-16 lg:h-16 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 lg:w-8 lg:h-8 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </div>
                        <h3 class="text-sm lg:text-lg font-semibold text-gray-900 mb-2">Satisfaction</h3>
                        <p class="text-xs lg:text-sm text-gray-600">100% satisfaction guaranteed always</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             SERVICES SECTION - Enhanced Cards
             ======================================== -->
        <section class="py-12 lg:py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between mb-10 lg:mb-12">
                    <div class="mb-4 sm:mb-0">
                        <span class="inline-block px-4 py-1.5 bg-pink-100 text-pink-700 text-sm font-medium rounded-full mb-4">
                            Our Services
                        </span>
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900">
                            Popular <span class="gradient-text">Services</span>
                        </h2>
                    </div>
                    <a href="/services" class="inline-flex items-center text-pink-600 hover:text-pink-700 font-medium group">
                        View All Services
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    <!-- Service Card 1 -->
                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden card-hover">
                        <div class="relative h-40 lg:h-48 bg-gradient-to-br from-pink-100 via-purple-50 to-indigo-100 overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-16 h-16 text-pink-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-gray-700">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                60 min
                            </div>
                        </div>
                        <div class="p-4 lg:p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg lg:text-xl font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Hair Styling</h3>
                                <span class="text-lg lg:text-xl font-bold text-pink-600">$50</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">Professional hair cuts, styling, and coloring services for all hair types.</p>
                            <a href="/book" class="inline-flex items-center justify-center w-full bg-gray-100 hover:bg-gradient-to-r hover:from-pink-600 hover:to-purple-600 text-gray-700 hover:text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                                Book Now
                            </a>
                        </div>
                    </div>

                    <!-- Service Card 2 -->
                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden card-hover">
                        <div class="relative h-40 lg:h-48 bg-gradient-to-br from-purple-100 via-pink-50 to-rose-100 overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-16 h-16 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-gray-700">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                45 min
                            </div>
                        </div>
                        <div class="p-4 lg:p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg lg:text-xl font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Nail Care</h3>
                                <span class="text-lg lg:text-xl font-bold text-pink-600">$30</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">Manicures, pedicures, and nail art services with premium products.</p>
                            <a href="/book" class="inline-flex items-center justify-center w-full bg-gray-100 hover:bg-gradient-to-r hover:from-pink-600 hover:to-purple-600 text-gray-700 hover:text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                                Book Now
                            </a>
                        </div>
                    </div>

                    <!-- Service Card 3 -->
                    <div class="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden card-hover">
                        <div class="relative h-40 lg:h-48 bg-gradient-to-br from-rose-100 via-orange-50 to-yellow-100 overflow-hidden">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-16 h-16 text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </div>
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-gray-700">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                90 min
                            </div>
                        </div>
                        <div class="p-4 lg:p-6">
                            <div class="flex items-start justify-between mb-2">
                                <h3 class="text-lg lg:text-xl font-semibold text-gray-900 group-hover:text-pink-600 transition-colors">Spa Treatments</h3>
                                <span class="text-lg lg:text-xl font-bold text-pink-600">$75</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">Relaxing facials and skin care treatments for ultimate rejuvenation.</p>
                            <a href="/book" class="inline-flex items-center justify-center w-full bg-gray-100 hover:bg-gradient-to-r hover:from-pink-600 hover:to-purple-600 text-gray-700 hover:text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all">
                                Book Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             STATISTICS COUNTER SECTION
             ======================================== -->
        <section class="py-12 lg:py-16 bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 left-1/4 w-64 h-64 bg-white rounded-full"></div>
                <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-white rounded-full"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-2">500+</div>
                        <div class="text-pink-200 text-sm lg:text-base">Happy Clients</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-2">50+</div>
                        <div class="text-pink-200 text-sm lg:text-base">Services Offered</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-2">15+</div>
                        <div class="text-pink-200 text-sm lg:text-base">Expert Stylists</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-2">5</div>
                        <div class="text-pink-200 text-sm lg:text-base">Years Experience</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================
             TESTIMONIALS SECTION
             ======================================== -->
        <section class="py-12 lg:py-20 bg-gradient-to-br from-gray-50 to-pink-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 lg:mb-16">
                    <span class="inline-block px-4 py-1.5 bg-rose-100 text-rose-700 text-sm font-medium rounded-full mb-4">
                        Testimonials
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                        What Our <span class="gradient-text">Clients Say</span>
                    </h2>
                    <p class="text-base lg:text-lg text-gray-600 max-w-2xl mx-auto">
                        Don't just take our word for it - hear from our happy customers.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                    @for($i = 0; $i < 3; $i++)
                        <div class="bg-white rounded-2xl p-6 lg:p-8 shadow-sm hover:shadow-lg transition-shadow relative">
                            <div class="absolute top-4 right-4 text-pink-100">
                                <svg class="w-10 h-10 lg:w-12 lg:h-12" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                            </div>
                            <div class="flex text-yellow-400 mb-4">
                                @for($j = 0; $j < 5; $j++)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <p class="text-gray-600 mb-6 text-sm lg:text-base leading-relaxed">"{{ ['Amazing experience! The staff was incredibly professional and the results exceeded my expectations. Will definitely be coming back!', 'Best salon in town! The attention to detail is remarkable and the atmosphere is so relaxing. Highly recommend!', 'I\'ve been a client for over a year now and every visit is consistently excellent. The team truly cares about their clients.'][$i] }}"</p>
                            <div class="flex items-center">
                                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-pink-100 to-purple-100 rounded-full flex items-center justify-center mr-3 lg:mr-4">
                                    <span class="text-pink-600 font-semibold text-sm lg:text-base">{{ ['S', 'M', 'J'][$i] }}</span>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 text-sm lg:text-base">{{ ['Sarah M.', 'Michael R.', 'Jennifer L.'][$i] }}</h4>
                                    <p class="text-xs lg:text-sm text-gray-500">Verified Client</p>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>

        <!-- ========================================
             CTA SECTION
             ======================================== -->
        <section class="py-12 lg:py-20 bg-gradient-to-br from-pink-50 via-purple-50 to-indigo-50 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-64 h-64 bg-pink-200 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-purple-200 rounded-full opacity-20 blur-3xl"></div>

            <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-4 lg:mb-6">
                    Ready to Look Your <span class="gradient-text">Best</span>?
                </h2>
                <p class="text-base lg:text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                    Book your appointment today and experience the transformation. Our expert stylists are ready to make you shine!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/book" class="inline-flex items-center justify-center bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white px-8 py-4 rounded-full text-base lg:text-lg font-semibold shadow-lg shadow-pink-500/30 hover:shadow-pink-500/50 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Book Your Appointment
                    </a>
                    <a href="tel:+15551234567" class="inline-flex items-center justify-center bg-white hover:bg-gray-50 text-gray-800 px-8 py-4 rounded-full text-base lg:text-lg font-semibold border-2 border-gray-200 hover:border-pink-300 transition-all">
                        <svg class="w-5 h-5 mr-2 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        Call Us Now
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- ========================================
         FOOTER
         ======================================== -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
                <!-- Brand Column -->
                <div class="col-span-2 md:col-span-1">
                    <span class="text-xl lg:text-2xl font-bold text-white mb-4 block">{{ config('app.name', 'BookingFlow') }}</span>
                    <p class="text-gray-400 text-sm mb-6">Professional beauty services in a welcoming environment. Your satisfaction is our priority.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-sm lg:text-base font-semibold mb-4 lg:mb-6">Quick Links</h3>
                    <ul class="space-y-2 lg:space-y-3">
                        <li><a href="/" class="text-gray-400 hover:text-white text-sm transition-colors">Home</a></li>
                        <li><a href="/services" class="text-gray-400 hover:text-white text-sm transition-colors">Services</a></li>
                        <li><a href="/gallery" class="text-gray-400 hover:text-white text-sm transition-colors">Gallery</a></li>
                        <li><a href="/book" class="text-gray-400 hover:text-white text-sm transition-colors">Book Online</a></li>
                        <li><a href="/contact" class="text-gray-400 hover:text-white text-sm transition-colors">Contact</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-sm lg:text-base font-semibold mb-4 lg:mb-6">Contact Us</h3>
                    <ul class="space-y-2 lg:space-y-3 text-sm">
                        <li class="flex items-start text-gray-400">
                            <svg class="w-5 h-5 mr-2 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            123 Beauty Street, Suite 100
                        </li>
                        <li class="flex items-center text-gray-400">
                            <svg class="w-5 h-5 mr-2 text-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:+15551234567" class="hover:text-white transition-colors">(555) 123-4567</a>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <svg class="w-5 h-5 mr-2 text-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <a href="mailto:info@bookingflow.com" class="hover:text-white transition-colors">info@bookingflow.com</a>
                        </li>
                    </ul>
                </div>

                <!-- Business Hours -->
                <div>
                    <h3 class="text-sm lg:text-base font-semibold mb-4 lg:mb-6">Business Hours</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li class="flex justify-between"><span>Mon - Fri</span><span>9AM - 7PM</span></li>
                        <li class="flex justify-between"><span>Saturday</span><span>9AM - 6PM</span></li>
                        <li class="flex justify-between"><span>Sunday</span><span>10AM - 5PM</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div class="border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ config('app.name', 'BookingFlow') }}. All rights reserved.</p>
                    <div class="flex space-x-6 mt-4 sm:mt-0">
                        <a href="/privacy" class="hover:text-white transition-colors">Privacy Policy</a>
                        <a href="/terms" class="hover:text-white transition-colors">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Book Button (Mobile) -->
    <div class="fixed bottom-6 right-6 lg:hidden z-40">
        <a href="/book" class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-pink-600 to-purple-600 text-white rounded-full shadow-lg shadow-pink-500/40 hover:shadow-pink-500/60 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </a>
    </div>
</body>
</html>
