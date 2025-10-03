<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->salon_name ?? 'Beauty Salon' }} - Contact Us</title>

    @if(isset($seoData))
        <meta name="description" content="{{ $seoData['description'] ?? '' }}">
        <meta name="keywords" content="{{ $seoData['keywords'] ?? '' }}">

        <!-- Open Graph -->
        <meta property="og:title" content="{{ $seoData['og_title'] ?? '' }}">
        <meta property="og:description" content="{{ $seoData['og_description'] ?? '' }}">
        <meta property="og:image" content="{{ $seoData['og_image'] ?? '' }}">
        <meta property="og:url" content="{{ $seoData['og_url'] ?? '' }}">
        <meta property="og:type" content="{{ $seoData['og_type'] ?? 'website' }}">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="{{ $seoData['twitter_card'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seoData['twitter_title'] ?? '' }}">
        <meta name="twitter:description" content="{{ $seoData['twitter_description'] ?? '' }}">
        <meta name="twitter:image" content="{{ $seoData['twitter_image'] ?? '' }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-text {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <x-salon-logo class="h-10 w-auto" />
                        <span class="text-2xl font-bold gradient-text">{{ $settings->salon_name ?? 'Beauty Salon' }}</span>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden">
                    <button type="button" id="mobile-menu-btn" class="text-gray-700 hover:text-pink-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Desktop navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Home</a>
                    <a href="{{ route('services') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Services</a>
                    <a href="{{ route('gallery') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Gallery</a>
                    <a href="{{ route('blog') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Blog</a>
                    <a href="{{ route('contact') }}" class="text-pink-600 font-medium">Contact</a>
                    <a href="{{ route('booking') }}" class="bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white px-6 py-2.5 rounded-full font-semibold transition-all shadow-md hover:shadow-lg">
                        Book Now
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Login</a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
            <div class="px-4 py-4 space-y-3">
                <a href="{{ route('home') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Home</a>
                <a href="{{ route('services') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Services</a>
                <a href="{{ route('gallery') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Gallery</a>
                <a href="{{ route('blog') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Blog</a>
                <a href="{{ route('contact') }}" class="block text-pink-600 font-medium">Contact</a>
                <a href="{{ route('booking') }}" class="block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-2.5 rounded-full font-semibold text-center">Book Now</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-br from-pink-50 via-purple-50 to-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-4">
                    Get In <span class="gradient-text">Touch</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    We'd love to hear from you. Visit us or reach out today!
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Send Us a Message</h2>
                <p class="text-lg text-gray-600">Fill out the form below and we'll get back to you as soon as possible</p>
            </div>
            <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                @livewire('contact-form')
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Other Ways to Reach Us</h2>
                <p class="text-lg text-gray-600">Prefer another method? Here's how you can contact us</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                @if($settings->salon_address)
                <div class="text-center p-8 bg-gradient-to-br from-pink-50 to-white rounded-2xl border border-pink-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-pink-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Visit Us</h3>
                    <p class="text-gray-600">{{ $settings->salon_address }}</p>
                    @if($settings->salon_city && $settings->salon_state)
                        <p class="text-gray-600">{{ $settings->salon_city }}, {{ $settings->salon_state }}</p>
                    @endif
                </div>
                @endif

                @if($settings->salon_phone)
                <div class="text-center p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-purple-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Call Us</h3>
                    <a href="tel:{{ $settings->salon_phone }}" class="text-gray-600 hover:text-pink-600 transition-colors">{{ $settings->salon_phone }}</a>
                </div>
                @endif

                @if($settings->salon_email)
                <div class="text-center p-8 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-blue-100">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Email Us</h3>
                    <a href="mailto:{{ $settings->salon_email }}" class="text-gray-600 hover:text-pink-600 transition-colors">{{ $settings->salon_email }}</a>
                </div>
                @endif
            </div>

            <!-- Business Hours -->
            @if($settings->business_hours)
            <div class="max-w-2xl mx-auto bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Business Hours</h2>
                <div class="space-y-3">
                    @php
                        $hours = is_string($settings->business_hours) ? json_decode($settings->business_hours, true) : $settings->business_hours;
                    @endphp
                    @if(is_array($hours))
                        @foreach($hours as $day => $time)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="font-semibold text-gray-700">{{ ucfirst($day) }}</span>
                                <span class="text-gray-600">{{ $time }}</span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-pink-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-extrabold mb-6">Ready to Book?</h2>
            <p class="text-xl mb-10 opacity-90">Schedule your appointment online in just a few clicks</p>
            <a href="{{ route('booking') }}" class="inline-block bg-white text-pink-600 px-10 py-4 rounded-full text-lg font-bold hover:bg-gray-100 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Book Your Appointment
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">{{ $settings->salon_name ?? 'Beauty Salon' }}</h3>
                    <p class="text-gray-400">Your trusted partner in beauty and wellness.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-pink-400">Home</a></li>
                        <li><a href="{{ route('services') }}" class="hover:text-pink-400">Services</a></li>
                        <li><a href="{{ route('gallery') }}" class="hover:text-pink-400">Gallery</a></li>
                        <li><a href="{{ route('blog') }}" class="hover:text-pink-400">Blog</a></li>
                        <li><a href="{{ route('booking') }}" class="hover:text-pink-400">Book Now</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        @if($settings->salon_phone)
                            <li>{{ $settings->salon_phone }}</li>
                        @endif
                        @if($settings->salon_email)
                            <li>{{ $settings->salon_email }}</li>
                        @endif
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Connect With Us</h4>
                    <div class="flex space-x-4">
                        @if($settings->facebook_url)
                        <a href="{{ $settings->facebook_url }}" class="text-gray-400 hover:text-pink-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        @endif
                        @if($settings->instagram_url)
                        <a href="{{ $settings->instagram_url }}" class="text-gray-400 hover:text-pink-400">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} {{ $settings->salon_name ?? 'Beauty Salon' }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
