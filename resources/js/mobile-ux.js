// Mobile UX Enhancements for Beauty Salon Management System

class MobileUX {
    constructor() {
        this.init();
    }

    init() {
        this.setupTouchInteractions();
        this.setupSwipeGestures();
        this.setupHapticFeedback();
        this.setupMobileNavigation();
        this.setupFormEnhancements();
        this.setupLoadingStates();
        this.setupToastNotifications();
        this.setupAccessibility();
    }

    // Enhanced Touch Interactions
    setupTouchInteractions() {
        // Add touch feedback to interactive elements
        document.addEventListener('touchstart', (e) => {
            const target = e.target.closest('.mobile-card, .mobile-btn, .mobile-service-card, .mobile-time-slot');
            if (target) {
                target.classList.add('haptic-light');
                setTimeout(() => target.classList.remove('haptic-light'), 100);
            }
        });

        // Prevent double-tap zoom on buttons
        document.addEventListener('touchend', (e) => {
            if (e.target.closest('.mobile-btn')) {
                e.preventDefault();
            }
        });
    }

    // Swipe Gesture Support
    setupSwipeGestures() {
        let startX, startY, endX, endY;
        const minSwipeDistance = 50;

        document.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        });

        document.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            endY = e.changedTouches[0].clientY;
            
            const deltaX = endX - startX;
            const deltaY = endY - startY;
            
            // Check if it's a horizontal swipe
            if (Math.abs(deltaX) > Math.abs(deltaY) && Math.abs(deltaX) > minSwipeDistance) {
                const swipeContainer = e.target.closest('.swipe-container');
                if (swipeContainer) {
                    if (deltaX > 0) {
                        this.handleSwipeRight(swipeContainer);
                    } else {
                        this.handleSwipeLeft(swipeContainer);
                    }
                }
            }
        });
    }

    handleSwipeLeft(container) {
        // Scroll to next item
        const scrollAmount = container.clientWidth * 0.8;
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    }

    handleSwipeRight(container) {
        // Scroll to previous item
        const scrollAmount = container.clientWidth * 0.8;
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }

    // Haptic Feedback Simulation
    setupHapticFeedback() {
        // Simulate haptic feedback for supported devices
        if ('vibrate' in navigator) {
            this.hapticLight = () => navigator.vibrate(10);
            this.hapticMedium = () => navigator.vibrate(25);
            this.hapticHeavy = () => navigator.vibrate(50);
        } else {
            // Fallback to visual feedback
            this.hapticLight = () => this.visualFeedback('light');
            this.hapticMedium = () => this.visualFeedback('medium');
            this.hapticHeavy = () => this.visualFeedback('heavy');
        }
    }

    visualFeedback(intensity) {
        const body = document.body;
        body.classList.add(`haptic-${intensity}`);
        setTimeout(() => body.classList.remove(`haptic-${intensity}`), 200);
    }

    // Mobile Navigation Enhancement
    setupMobileNavigation() {
        const navItems = document.querySelectorAll('.mobile-nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', (e) => {
                // Add haptic feedback
                this.hapticLight();
                
                // Update active state
                navItems.forEach(nav => nav.classList.remove('active'));
                item.classList.add('active');
            });
        });
    }

    // Form Enhancements
    setupFormEnhancements() {
        // Auto-focus on first input when form loads
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            const firstInput = form.querySelector('input, textarea, select');
            if (firstInput) {
                // Delay to ensure form is fully rendered
                setTimeout(() => firstInput.focus(), 100);
            }
        });

        // Enhanced input validation feedback
        const inputs = document.querySelectorAll('.mobile-input');
        inputs.forEach(input => {
            input.addEventListener('blur', () => {
                this.validateInput(input);
            });

            input.addEventListener('input', () => {
                this.clearValidationError(input);
            });
        });
    }

    validateInput(input) {
        const value = input.value.trim();
        const isRequired = input.hasAttribute('required');
        const type = input.type;

        if (isRequired && !value) {
            this.showInputError(input, 'This field is required');
            return false;
        }

        if (type === 'email' && value && !this.isValidEmail(value)) {
            this.showInputError(input, 'Please enter a valid email address');
            return false;
        }

        if (type === 'tel' && value && !this.isValidPhone(value)) {
            this.showInputError(input, 'Please enter a valid phone number');
            return false;
        }

        this.clearValidationError(input);
        return true;
    }

    showInputError(input, message) {
        input.classList.add('border-red-500');
        let errorElement = input.parentNode.querySelector('.input-error');
        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'input-error text-red-500 text-sm mt-1';
            input.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    clearValidationError(input) {
        input.classList.remove('border-red-500');
        const errorElement = input.parentNode.querySelector('.input-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        return /^[\+]?[1-9][\d]{0,15}$/.test(phone.replace(/\s/g, ''));
    }

    // Loading States
    setupLoadingStates() {
        // Show loading state for async operations
        document.addEventListener('livewire:load', () => {
            Livewire.on('showLoading', () => {
                this.showLoading();
            });

            Livewire.on('hideLoading', () => {
                this.hideLoading();
            });
        });
    }

    showLoading(message = 'Loading...') {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.id = 'mobile-loading-overlay';
        loadingOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        loadingOverlay.innerHTML = `
            <div class="bg-white rounded-lg p-6 text-center">
                <div class="mobile-spinner mx-auto mb-4"></div>
                <p class="text-gray-700">${message}</p>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    }

    hideLoading() {
        const loadingOverlay = document.getElementById('mobile-loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }

    // Toast Notifications
    setupToastNotifications() {
        // Auto-dismiss toasts after 5 seconds
        const toasts = document.querySelectorAll('.mobile-toast');
        toasts.forEach(toast => {
            setTimeout(() => {
                toast.style.animation = 'slideUp 0.3s ease-out reverse';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        });
    }

    showToast(message, type = 'info', duration = 5000) {
        const toast = document.createElement('div');
        toast.className = `mobile-toast ${type === 'error' ? 'bg-red-50 border-red-200' : 'bg-green-50 border-green-200'}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'error' ? 
                        '<svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                        '<svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium ${type === 'error' ? 'text-red-800' : 'text-green-800'}">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-dismiss
        setTimeout(() => {
            toast.style.animation = 'slideUp 0.3s ease-out reverse';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // Accessibility Enhancements
    setupAccessibility() {
        // Add keyboard navigation support
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const target = e.target;
                if (target.classList.contains('mobile-card') || target.classList.contains('mobile-btn')) {
                    e.preventDefault();
                    target.click();
                }
            }
        });

        // Announce dynamic content changes
        this.announce = (message) => {
            const announcement = document.createElement('div');
            announcement.setAttribute('aria-live', 'polite');
            announcement.setAttribute('aria-atomic', 'true');
            announcement.className = 'sr-only';
            announcement.textContent = message;
            document.body.appendChild(announcement);
            setTimeout(() => announcement.remove(), 1000);
        };
    }

    // Utility Methods
    isMobile() {
        return window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    getDeviceType() {
        if (this.isMobile()) {
            return 'mobile';
        } else if (window.innerWidth <= 1024) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    // Performance optimizations
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
}

// Initialize Mobile UX when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.mobileUX = new MobileUX();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = MobileUX;
}
