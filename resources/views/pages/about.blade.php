<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $settings->salon_name ?? 'Beauty Salon' }} - About Us</title>

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
                    <a href="{{ route('contact') }}" class="text-gray-700 hover:text-pink-600 font-medium transition-colors">Contact</a>
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
                <a href="{{ route('contact') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Contact</a>
                <a href="{{ route('booking') }}" class="block bg-gradient-to-r from-pink-600 to-purple-600 text-white px-6 py-2.5 rounded-full font-semibold text-center">Book Now</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Login</a>
                    <a href="{{ route('register') }}" class="block text-gray-700 hover:text-pink-600 font-medium">Register</a>
                @endauth
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
            <a href="{{ route(booking) }}" class="inline-block bg-white text-pink-600 px-10 py-4 rounded-full text-lg font-bold hover:bg-gray-100 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                Schedule Your Visit
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
