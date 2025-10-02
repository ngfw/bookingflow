<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seoData['title'] }}</title>
    <meta name="description" content="{{ $seoData['description'] }}">
    <meta name="keywords" content="{{ $seoData['keywords'] }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $seoData['og_title'] }}">
    <meta property="og:description" content="{{ $seoData['og_description'] }}">
    <meta property="og:image" content="{{ $seoData['og_image'] }}">
    <meta property="og:url" content="{{ $seoData['og_url'] }}">
    <meta property="og:type" content="{{ $seoData['og_type'] }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="{{ $seoData['twitter_card'] }}">
    <meta name="twitter:title" content="{{ $seoData['twitter_title'] }}">
    <meta name="twitter:description" content="{{ $seoData['twitter_description'] }}">
    <meta name="twitter:image" content="{{ $seoData['twitter_image'] }}">

    <!-- Structured Data -->
    {!! $this->seoService->generateStructuredDataJSON($structuredData) !!}

    <!-- Salon Theme CSS -->
    <style>
        {!! $settings->getThemeCss() !!}
    </style>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family={{ $settings->font_family }}:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Favicon -->
    @if($settings->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $settings->favicon_path) }}">
    @endif
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        @if($settings->logo_path)
                            <img class="h-8 w-auto" src="{{ asset('storage/' . $settings->logo_path) }}" alt="{{ $settings->salon_name }}">
                        @else
                            <span class="text-2xl font-bold text-pink-600">{{ $settings->salon_name }}</span>
                        @endif
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
                    <!-- Social Links -->
                    <div class="hidden md:flex md:space-x-4">
                        @if($settings->social_links['facebook'])
                            <a href="{{ $settings->social_links['facebook'] }}" target="_blank" class="text-gray-400 hover:text-pink-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        @endif
                        @if($settings->social_links['instagram'])
                            <a href="{{ $settings->social_links['instagram'] }}" target="_blank" class="text-gray-400 hover:text-pink-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.281c-.49 0-.928-.175-1.297-.49-.368-.315-.49-.753-.49-1.243 0-.49.122-.928.49-1.243.369-.315.807-.49 1.297-.49s.928.175 1.297.49c.368.315.49.753.49 1.243 0 .49-.122.928-.49 1.243-.369.315-.807.49-1.297.49z"/>
                                </svg>
                            </a>
                        @endif
                    </div>

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
        @if(isset($page))
            <!-- Dynamic Page Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Page Header -->
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $page->title }}</h1>
                    @if($page->excerpt)
                        <p class="text-xl text-gray-600 max-w-3xl mx-auto">{{ $page->excerpt }}</p>
                    @endif
                </div>

                <!-- Page Sections -->
                @if($page->sections->count() > 0)
                    @foreach($page->sections as $section)
                        @include('sections.' . $section->section_type, $section->getRenderData())
                    @endforeach
                @else
                    <!-- Fallback to page content -->
                    <div class="prose max-w-none">
                        {!! $page->content !!}
                    </div>
                @endif
            </div>
        @else
            <!-- Homepage Content -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Hero Section -->
                <section class="bg-gradient-to-r from-pink-100 to-purple-100 py-20 rounded-lg mb-12">
                    <div class="text-center">
                        <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                            Welcome to <span class="text-pink-600">{{ $settings->salon_name }}</span>
                        </h1>
                        <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                            {{ $settings->salon_description }}
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
                </section>

                <!-- Services Section -->
                <section class="mb-12">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Services</h2>
                        <p class="text-lg text-gray-600">Professional beauty services tailored to your needs</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach(\App\Models\Service::where('is_active', true)->limit(6)->get() as $service)
                            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $service->name }}</h3>
                                <p class="text-gray-600 mb-4">{{ Str::limit($service->description, 100) }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-pink-600">${{ number_format($service->price, 2) }}</span>
                                    <span class="text-sm text-gray-500">{{ $service->duration_minutes }} min</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <!-- Gallery Section -->
                <section class="mb-12">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Work</h2>
                        <p class="text-lg text-gray-600">See the beautiful transformations we create</p>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach(\App\Models\Gallery::where('is_active', true)->limit(8)->get() as $gallery)
                            @if($gallery->thumbnail)
                                <div class="aspect-w-1 aspect-h-1">
                                    <img src="{{ asset('storage/' . $gallery->thumbnail) }}" 
                                         alt="{{ $gallery->name }}" 
                                         class="w-full h-full object-cover rounded-lg">
                                </div>
                            @endif
                        @endforeach
                    </div>
                </section>

                <!-- Testimonials Section -->
                <section class="mb-12">
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">What Our Clients Say</h2>
                        <p class="text-lg text-gray-600">Real experiences from our satisfied customers</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach(\App\Models\Testimonial::where('is_active', true)->limit(3)->get() as $testimonial)
                            <div class="bg-white rounded-lg shadow-sm p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center mr-4">
                                        <span class="text-pink-600 font-semibold">{{ $testimonial->client_initials }}</span>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $testimonial->client_name }}</h4>
                                        <div class="flex items-center">
                                            {!! $testimonial->star_rating !!}
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-600">"{{ $testimonial->content }}"</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Salon Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ $settings->salon_name }}</h3>
                    <p class="text-gray-400 mb-4">{{ $settings->salon_description }}</p>
                    @if($settings->contact_info['address'])
                        <p class="text-gray-400">{{ $settings->contact_info['address'] }}</p>
                    @endif
                </div>

                <!-- Contact Info -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    @if($settings->contact_info['phone'])
                        <p class="text-gray-400 mb-2">{{ $settings->contact_info['phone'] }}</p>
                    @endif
                    @if($settings->contact_info['email'])
                        <p class="text-gray-400 mb-2">{{ $settings->contact_info['email'] }}</p>
                    @endif
                </div>

                <!-- Hours -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Hours</h3>
                    @if($settings->contact_info['hours'])
                        @foreach($settings->contact_info['hours'] as $day => $hours)
                            <p class="text-gray-400 text-sm">{{ ucfirst($day) }}: {{ $hours }}</p>
                        @endforeach
                    @endif
                </div>

                <!-- Social Links -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        @if($settings->social_links['facebook'])
                            <a href="{{ $settings->social_links['facebook'] }}" target="_blank" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>
                        @endif
                        @if($settings->social_links['instagram'])
                            <a href="{{ $settings->social_links['instagram'] }}" target="_blank" class="text-gray-400 hover:text-white">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987s11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.198 14.895 3.708 13.744 3.708 12.447s.49-2.448 1.418-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.928.875 1.418 2.026 1.418 3.323s-.49 2.448-1.418 3.244c-.875.807-2.026 1.297-3.323 1.297zm7.83-9.281c-.49 0-.928-.175-1.297-.49-.368-.315-.49-.753-.49-1.243 0-.49.122-.928.49-1.243.369-.315.807-.49 1.297-.49s.928.175 1.297.49c.368.315.49.753.49 1.243 0 .49-.122.928-.49 1.243-.369.315-.807.49-1.297.49z"/>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} {{ $settings->salon_name }}. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
