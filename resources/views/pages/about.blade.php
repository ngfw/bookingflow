<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->salon_name ?? 'service business' }} - About Us</title>

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
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 lg:h-20">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center space-x-3">
                        <x-salon-logo class="h-8 lg:h-10 w-auto" />
                        <span class="text-xl lg:text-2xl font-bold gradient-text">{{ $settings->salon_name ?? 'service business' }}</span>
                    </div>

                    <!-- Desktop Navigation Links -->
                    <div class="hidden lg:ml-10 lg:flex lg:space-x-8">
                        <a href="{{ route('home') }}" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Home</a>
                        <a href="{{ route('services') }}" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Services</a>
                        <a href="{{ route('gallery') }}" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Gallery</a>
                        <a href="{{ route('blog') }}" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Blog</a>
                        <a href="{{ route('contact') }}" class="text-gray-500 hover:text-pink-600 px-3 py-2 text-sm font-medium border-b-2 border-transparent hover:border-pink-300 transition-colors">Contact</a>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    @if($settings->salon_phone)
                        <a href="tel:{{ $settings->salon_phone }}" class="hidden lg:flex items-center text-gray-600 hover:text-pink-600 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="text-sm font-medium">{{ $settings->salon_phone }}</span>
                        </a>
                    @endif

                    <a href="{{ route('booking') }}" class="bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white px-4 py-2 lg:px-6 lg:py-2.5 rounded-full text-sm font-medium shadow-lg shadow-pink-500/25 hover:shadow-pink-500/40 transition-all">
                        Book Now
                    </a>

                    <button type="button" id="mobile-menu-btn" class="lg:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden lg:hidden bg-white border-t">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">Home</a>
                <a href="{{ route('services') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">Services</a>
                <a href="{{ route('gallery') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">Gallery</a>
                <a href="{{ route('blog') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">Blog</a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-pink-600 hover:bg-gray-50 rounded-md">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="bg-gradient-to-br from-pink-50 via-purple-50 to-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 mb-4">
                    {{ $settings->about_us_title ?? "About Us" }}
                </h1>
            </div>
        </div>
    </section>

    <!-- About Us Content Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($settings->about_us_image)
                <div class="mb-16">
                    <img src="{{ Storage::url($settings->about_us_image) }}" alt="About Us" class="w-full h-96 object-cover rounded-2xl shadow-2xl">
                </div>
            @endif

            <div class="prose prose-lg max-w-4xl mx-auto mb-16">
                @if($settings->about_us_content)
                    <div class="text-gray-700 leading-relaxed whitespace-pre-wrap">{!! nl2br(e($settings->about_us_content)) !!}</div>
                @else
                    <p class="text-gray-600">Welcome to our salon! We are passionate about providing exceptional beauty services.</p>
                @endif
            </div>

            @if($settings->about_us_mission || $settings->about_us_vision)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
                    @if($settings->about_us_mission)
                        <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Mission</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $settings->about_us_mission }}</p>
                        </div>
                    @endif

                    @if($settings->about_us_vision)
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $settings->about_us_vision }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </section>

    <!-- Team Section -->
    @if($teamMembers->count() > 0)
        <section class="py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4">
                        Meet Our <span class="gradient-text">Team</span>
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        Our talented professionals are here to help you look and feel your best
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    @foreach($teamMembers as $member)
                        <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all transform hover:-translate-y-1">
                            <div class="relative h-64 bg-gradient-to-br from-pink-100 to-purple-100">
                                @if($member->profile_image)
                                    <img src="{{ Storage::url($member->profile_image) }}" alt="{{ $member->user->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $member->user->name }}</h3>
                                <p class="text-pink-600 font-semibold mb-3">{{ $member->position }}</p>
                                @if($member->specializations)
                                    @php
                                        $specs = is_string($member->specializations) ? json_decode($member->specializations, true) : $member->specializations;
                                        $specs = is_array($specs) ? $specs : [];
                                    @endphp
                                    @if(count($specs) > 0)
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach(array_slice($specs, 0, 3) as $spec)
                                                <span class="bg-pink-100 text-pink-700 text-xs px-2 py-1 rounded-full">{{ $spec }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                                @if($member->bio)
                                    <p class="text-gray-600 text-sm line-clamp-3">{{ $member->bio }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-pink-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-extrabold mb-6">Ready to Transform Your Look?</h2>
            <p class="text-xl mb-10 opacity-90">Book your appointment today and experience our premium services</p>
            <a href="{{ route('booking') }}" class="inline-block bg-white text-pink-600 px-10 py-4 rounded-full text-lg font-bold hover:bg-gray-100 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Schedule Your Visit
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12">
                <div class="col-span-2 md:col-span-1">
                    <span class="text-xl lg:text-2xl font-bold text-white mb-4 block">{{ $settings->salon_name ?? 'service business' }}</span>
                    <p class="text-gray-400 text-sm mb-6">Your trusted partner in beauty and wellness. Professional services in a welcoming environment.</p>
                    <div class="flex space-x-4">
                        @if($settings->facebook_url)
                            <a href="{{ $settings->facebook_url }}" class="w-10 h-10 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                        @endif
                        @if($settings->instagram_url)
                            <a href="{{ $settings->instagram_url }}" class="w-10 h-10 bg-gray-800 hover:bg-pink-600 rounded-full flex items-center justify-center transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="text-sm lg:text-base font-semibold mb-4 lg:mb-6">Quick Links</h3>
                    <ul class="space-y-2 lg:space-y-3">
                        <li><a href="{{ route('home') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Home</a></li>
                        <li><a href="{{ route('services') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Services</a></li>
                        <li><a href="{{ route('gallery') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Gallery</a></li>
                        <li><a href="{{ route('booking') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Book Online</a></li>
                        <li><a href="{{ route('contact') }}" class="text-gray-400 hover:text-white text-sm transition-colors">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm lg:text-base font-semibold mb-4 lg:mb-6">Contact Us</h3>
                    <ul class="space-y-2 lg:space-y-3 text-sm">
                        @if($settings->salon_address)
                            <li class="flex items-start text-gray-400">
                                <svg class="w-5 h-5 mr-2 text-pink-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $settings->salon_address }}
                            </li>
                        @endif
                        @if($settings->salon_phone)
                            <li class="flex items-center text-gray-400">
                                <svg class="w-5 h-5 mr-2 text-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <a href="tel:{{ $settings->salon_phone }}" class="hover:text-white transition-colors">{{ $settings->salon_phone }}</a>
                            </li>
                        @endif
                        @if($settings->salon_email)
                            <li class="flex items-center text-gray-400">
                                <svg class="w-5 h-5 mr-2 text-pink-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <a href="mailto:{{ $settings->salon_email }}" class="hover:text-white transition-colors">{{ $settings->salon_email }}</a>
                            </li>
                        @endif
                    </ul>
                </div>

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

        <div class="border-t border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-400">
                    <p>&copy; {{ date('Y') }} {{ $settings->salon_name ?? 'service business' }}. All rights reserved.</p>
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
        <a href="{{ route('booking') }}" class="flex items-center justify-center w-14 h-14 bg-gradient-to-r from-pink-600 to-purple-600 text-white rounded-full shadow-lg shadow-pink-500/40 hover:shadow-pink-500/60 transition-all">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </a>
    </div>

    <script>
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
