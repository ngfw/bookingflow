// Enhanced Navigation JavaScript for Beauty Salon Management System

class NavigationManager {
    constructor() {
        this.init();
        this.isMobileMenuOpen = false;
        this.activeDropdown = null;
    }

    init() {
        this.setupMobileNavigation();
        this.setupDropdowns();
        this.setupKeyboardNavigation();
        this.setupBreadcrumbs();
        this.setupActiveStates();
        this.setupScrollEffects();
        this.setupAccessibility();
    }

    // Mobile Navigation
    setupMobileNavigation() {
        const toggleBtn = document.querySelector('.nav-toggle-btn');
        const navMenu = document.querySelector('.nav-menu');
        const overlay = document.querySelector('.mobile-nav-overlay');

        if (toggleBtn && navMenu) {
            toggleBtn.addEventListener('click', () => {
                this.toggleMobileMenu();
            });

            // Close menu when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', () => {
                    this.closeMobileMenu();
                });
            }

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!navMenu.contains(e.target) && !toggleBtn.contains(e.target)) {
                    this.closeMobileMenu();
                }
            });

            // Close menu on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.isMobileMenuOpen) {
                    this.closeMobileMenu();
                }
            });
        }
    }

    toggleMobileMenu() {
        const toggleBtn = document.querySelector('.nav-toggle-btn');
        const navMenu = document.querySelector('.nav-menu');
        const overlay = document.querySelector('.mobile-nav-overlay');

        this.isMobileMenuOpen = !this.isMobileMenuOpen;

        if (this.isMobileMenuOpen) {
            this.openMobileMenu();
        } else {
            this.closeMobileMenu();
        }
    }

    openMobileMenu() {
        const toggleBtn = document.querySelector('.nav-toggle-btn');
        const navMenu = document.querySelector('.nav-menu');
        const overlay = document.querySelector('.mobile-nav-overlay');

        toggleBtn.setAttribute('aria-expanded', 'true');
        navMenu.classList.add('active');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus first menu item
        const firstLink = navMenu.querySelector('.nav-link');
        if (firstLink) {
            firstLink.focus();
        }
    }

    closeMobileMenu() {
        const toggleBtn = document.querySelector('.nav-toggle-btn');
        const navMenu = document.querySelector('.nav-menu');
        const overlay = document.querySelector('.mobile-nav-overlay');

        this.isMobileMenuOpen = false;
        toggleBtn.setAttribute('aria-expanded', 'false');
        navMenu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';

        // Return focus to toggle button
        toggleBtn.focus();
    }

    // Dropdown Navigation
    setupDropdowns() {
        const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle, .user-menu-toggle');

        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleDropdown(toggle);
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!toggle.contains(e.target) && !toggle.nextElementSibling?.contains(e.target)) {
                    this.closeDropdown(toggle);
                }
            });
        });

        // Handle dropdown items
        const dropdownLinks = document.querySelectorAll('.dropdown-link');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Close mobile menu if open
                if (this.isMobileMenuOpen) {
                    this.closeMobileMenu();
                }
            });
        });
    }

    toggleDropdown(toggle) {
        const isOpen = toggle.getAttribute('aria-expanded') === 'true';
        
        if (isOpen) {
            this.closeDropdown(toggle);
        } else {
            this.openDropdown(toggle);
        }
    }

    openDropdown(toggle) {
        // Close other dropdowns
        this.closeAllDropdowns();
        
        toggle.setAttribute('aria-expanded', 'true');
        this.activeDropdown = toggle;

        // Focus first dropdown item
        const dropdown = toggle.nextElementSibling;
        if (dropdown) {
            const firstLink = dropdown.querySelector('.dropdown-link');
            if (firstLink) {
                setTimeout(() => firstLink.focus(), 100);
            }
        }
    }

    closeDropdown(toggle) {
        toggle.setAttribute('aria-expanded', 'false');
        if (this.activeDropdown === toggle) {
            this.activeDropdown = null;
        }
    }

    closeAllDropdowns() {
        const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle, .user-menu-toggle');
        dropdownToggles.forEach(toggle => {
            this.closeDropdown(toggle);
        });
    }

    // Keyboard Navigation
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Handle dropdown navigation
            if (this.activeDropdown) {
                this.handleDropdownKeyboard(e);
            }

            // Handle mobile menu navigation
            if (this.isMobileMenuOpen) {
                this.handleMobileMenuKeyboard(e);
            }
        });
    }

    handleDropdownKeyboard(e) {
        const dropdown = this.activeDropdown.nextElementSibling;
        if (!dropdown) return;

        const links = Array.from(dropdown.querySelectorAll('.dropdown-link'));
        const currentIndex = links.indexOf(document.activeElement);

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = currentIndex < links.length - 1 ? currentIndex + 1 : 0;
                links[nextIndex].focus();
                break;
            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : links.length - 1;
                links[prevIndex].focus();
                break;
            case 'Escape':
                e.preventDefault();
                this.closeDropdown(this.activeDropdown);
                this.activeDropdown.focus();
                break;
            case 'Tab':
                if (e.shiftKey && currentIndex === 0) {
                    e.preventDefault();
                    this.closeDropdown(this.activeDropdown);
                    this.activeDropdown.focus();
                } else if (!e.shiftKey && currentIndex === links.length - 1) {
                    e.preventDefault();
                    this.closeDropdown(this.activeDropdown);
                }
                break;
        }
    }

    handleMobileMenuKeyboard(e) {
        const navMenu = document.querySelector('.nav-menu');
        if (!navMenu) return;

        const focusableElements = Array.from(navMenu.querySelectorAll(
            'a, button, [tabindex]:not([tabindex="-1"])'
        ));
        const currentIndex = focusableElements.indexOf(document.activeElement);

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                const nextIndex = currentIndex < focusableElements.length - 1 ? currentIndex + 1 : 0;
                focusableElements[nextIndex].focus();
                break;
            case 'ArrowUp':
                e.preventDefault();
                const prevIndex = currentIndex > 0 ? currentIndex - 1 : focusableElements.length - 1;
                focusableElements[prevIndex].focus();
                break;
            case 'Escape':
                e.preventDefault();
                this.closeMobileMenu();
                break;
        }
    }

    // Breadcrumb Navigation
    setupBreadcrumbs() {
        const breadcrumbLinks = document.querySelectorAll('.breadcrumb-link');
        
        breadcrumbLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                // Add loading state
                link.classList.add('loading');
                
                // Remove loading state after navigation
                setTimeout(() => {
                    link.classList.remove('loading');
                }, 1000);
            });
        });
    }

    // Active States
    setupActiveStates() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.nav-link');

        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href && currentPath.startsWith(href) && href !== '/') {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            } else if (href === '/' && currentPath === '/') {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            }
        });
    }

    // Scroll Effects
    setupScrollEffects() {
        let lastScrollY = window.scrollY;
        const nav = document.querySelector('.enhanced-nav');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            if (currentScrollY > 100) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }

            // Hide/show nav on scroll
            if (currentScrollY > lastScrollY && currentScrollY > 200) {
                nav.classList.add('nav-hidden');
            } else {
                nav.classList.remove('nav-hidden');
            }

            lastScrollY = currentScrollY;
        });
    }

    // Accessibility
    setupAccessibility() {
        // Add ARIA labels where missing
        const navLinks = document.querySelectorAll('.nav-link:not([aria-label])');
        navLinks.forEach(link => {
            const text = link.querySelector('.nav-text');
            if (text) {
                link.setAttribute('aria-label', text.textContent);
            }
        });

        // Handle focus management
        document.addEventListener('focusin', (e) => {
            if (e.target.closest('.nav-menu')) {
                document.body.classList.add('nav-focused');
            } else {
                document.body.classList.remove('nav-focused');
            }
        });

        // Announce navigation changes to screen readers
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'aria-current') {
                    const target = mutation.target;
                    if (target.getAttribute('aria-current') === 'page') {
                        this.announceNavigation(target);
                    }
                }
            });
        });

        observer.observe(document.body, {
            attributes: true,
            subtree: true,
            attributeFilter: ['aria-current']
        });
    }

    announceNavigation(element) {
        const text = element.querySelector('.nav-text') || element.textContent;
        if (text && window.accessibilityManager) {
            window.accessibilityManager.announceToScreenReader(`Navigated to ${text}`, 'status');
        }
    }

    // Utility Methods
    getCurrentPage() {
        return window.location.pathname;
    }

    navigateTo(url) {
        window.location.href = url;
    }

    // Public API
    openMobileMenu() {
        this.openMobileMenu();
    }

    closeMobileMenu() {
        this.closeMobileMenu();
    }

    toggleMobileMenu() {
        this.toggleMobileMenu();
    }

    closeAllDropdowns() {
        this.closeAllDropdowns();
    }

    // Event Listeners for External Use
    onMobileMenuToggle(callback) {
        document.addEventListener('mobile-menu-toggle', callback);
    }

    onDropdownToggle(callback) {
        document.addEventListener('dropdown-toggle', callback);
    }

    onNavigationChange(callback) {
        document.addEventListener('navigation-change', callback);
    }
}

// Initialize Navigation Manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.navigationManager = new NavigationManager();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = NavigationManager;
}
