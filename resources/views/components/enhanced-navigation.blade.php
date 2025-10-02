{{-- Enhanced Navigation Component --}}

<nav class="enhanced-nav" role="navigation" aria-label="Main navigation">
    <!-- Mobile Navigation Toggle -->
    <div class="mobile-nav-toggle lg:hidden">
        <button class="nav-toggle-btn" aria-label="Toggle navigation menu" aria-expanded="false">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </div>

    <!-- Logo/Brand -->
    <div class="nav-brand">
        <a href="/" class="brand-link" aria-label="Beauty Salon - Home">
            <img src="/images/logo.png" alt="Beauty Salon Logo" class="brand-logo">
            <span class="brand-text">Beauty Salon</span>
        </a>
    </div>

    <!-- Main Navigation Menu -->
    <div class="nav-menu" id="main-navigation">
        <ul class="nav-list" role="menubar">
            <!-- Home -->
            <li class="nav-item" role="none">
                <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}" role="menuitem" aria-current="{{ request()->is('/') ? 'page' : 'false' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="nav-text">Home</span>
                </a>
            </li>

            <!-- Services -->
            <li class="nav-item nav-item-dropdown" role="none">
                <button class="nav-link nav-dropdown-toggle" role="menuitem" aria-expanded="false" aria-haspopup="true">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span class="nav-text">Services</span>
                    <svg class="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="nav-dropdown" role="menu" aria-label="Services submenu">
                    <div class="dropdown-content">
                        <a href="/services/hair" class="dropdown-link" role="menuitem">
                            <div class="dropdown-item">
                                <div class="dropdown-icon">💇</div>
                                <div class="dropdown-text">
                                    <div class="dropdown-title">Hair Services</div>
                                    <div class="dropdown-desc">Cut, color, styling</div>
                                </div>
                            </div>
                        </a>
                        <a href="/services/skin" class="dropdown-link" role="menuitem">
                            <div class="dropdown-item">
                                <div class="dropdown-icon">✨</div>
                                <div class="dropdown-text">
                                    <div class="dropdown-title">Skin Care</div>
                                    <div class="dropdown-desc">Facials, treatments</div>
                                </div>
                            </div>
                        </a>
                        <a href="/services/nails" class="dropdown-link" role="menuitem">
                            <div class="dropdown-item">
                                <div class="dropdown-icon">💅</div>
                                <div class="dropdown-text">
                                    <div class="dropdown-title">Nail Care</div>
                                    <div class="dropdown-desc">Manicure, pedicure</div>
                                </div>
                            </div>
                        </a>
                        <a href="/services/massage" class="dropdown-link" role="menuitem">
                            <div class="dropdown-item">
                                <div class="dropdown-icon">🧘</div>
                                <div class="dropdown-text">
                                    <div class="dropdown-title">Massage</div>
                                    <div class="dropdown-desc">Relaxation, therapy</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </li>

            <!-- Book Appointment -->
            <li class="nav-item" role="none">
                <a href="/book" class="nav-link nav-link-cta {{ request()->is('book*') ? 'active' : '' }}" role="menuitem" aria-current="{{ request()->is('book*') ? 'page' : 'false' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="nav-text">Book Now</span>
                </a>
            </li>

            <!-- About -->
            <li class="nav-item" role="none">
                <a href="/about" class="nav-link {{ request()->is('about*') ? 'active' : '' }}" role="menuitem" aria-current="{{ request()->is('about*') ? 'page' : 'false' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="nav-text">About</span>
                </a>
            </li>

            <!-- Contact -->
            <li class="nav-item" role="none">
                <a href="/contact" class="nav-link {{ request()->is('contact*') ? 'active' : '' }}" role="menuitem" aria-current="{{ request()->is('contact*') ? 'page' : 'false' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="nav-text">Contact</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- User Actions -->
    <div class="nav-actions">
        @auth
            <!-- User Menu -->
            <div class="user-menu">
                <button class="user-menu-toggle" aria-label="User menu" aria-expanded="false" aria-haspopup="true">
                    <div class="user-avatar">
                        <img src="{{ auth()->user()->avatar ?? '/images/default-avatar.png' }}" alt="{{ auth()->user()->name }}">
                    </div>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                    <svg class="dropdown-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div class="user-dropdown" role="menu" aria-label="User menu">
                    <div class="dropdown-content">
                        <a href="/profile" class="dropdown-link" role="menuitem">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>My Profile</span>
                        </a>
                        <a href="/appointments" class="dropdown-link" role="menuitem">
                            <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>My Appointments</span>
                        </a>
                        @if(auth()->user()->hasRole('admin'))
                            <a href="/admin/dashboard" class="dropdown-link" role="menuitem">
                                <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span>Admin Dashboard</span>
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                            @csrf
                            <button type="submit" class="dropdown-link dropdown-logout" role="menuitem">
                                <svg class="dropdown-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                <span>Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Login/Register -->
            <div class="auth-actions">
                <a href="/login" class="auth-link auth-link-login">Login</a>
                <a href="/register" class="auth-link auth-link-register">Sign Up</a>
            </div>
        @endauth
    </div>
</nav>

<!-- Breadcrumb Navigation -->
@if(isset($breadcrumbs) && count($breadcrumbs) > 1)
    <nav class="breadcrumb-nav" aria-label="Breadcrumb">
        <ol class="breadcrumb-list">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="breadcrumb-item">
                    @if($index === count($breadcrumbs) - 1)
                        <span class="breadcrumb-current" aria-current="page">{{ $breadcrumb['title'] }}</span>
                    @else
                        <a href="{{ $breadcrumb['url'] }}" class="breadcrumb-link">{{ $breadcrumb['title'] }}</a>
                        <svg class="breadcrumb-separator" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay" aria-hidden="true"></div>

<!-- Skip Links -->
<a href="#main-content" class="skip-link">Skip to main content</a>
<a href="#main-navigation" class="skip-link">Skip to navigation</a>
<a href="#footer" class="skip-link">Skip to footer</a>
