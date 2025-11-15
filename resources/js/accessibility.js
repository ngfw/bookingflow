// Accessibility Enhancements for BookingFlow Management System

class AccessibilityManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupSkipLinks();
        this.setupFocusManagement();
        this.setupKeyboardNavigation();
        this.setupARIALiveRegions();
        this.setupFormAccessibility();
        this.setupModalAccessibility();
        this.setupTableAccessibility();
        this.setupColorContrast();
        this.setupScreenReaderSupport();
        this.setupReducedMotion();
    }

    // Skip Links
    setupSkipLinks() {
        // Add skip links to main content areas
        const skipLinks = [
            { href: '#main-content', text: 'Skip to main content' },
            { href: '#navigation', text: 'Skip to navigation' },
            { href: '#footer', text: 'Skip to footer' }
        ];

        skipLinks.forEach(link => {
            const skipLink = document.createElement('a');
            skipLink.href = link.href;
            skipLink.textContent = link.text;
            skipLink.className = 'skip-link';
            skipLink.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(link.href);
                if (target) {
                    target.focus();
                    target.scrollIntoView();
                }
            });
            document.body.insertBefore(skipLink, document.body.firstChild);
        });
    }

    // Focus Management
    setupFocusManagement() {
        // Trap focus in modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                const modal = document.querySelector('.modal-overlay[aria-hidden="false"]');
                if (modal) {
                    this.trapFocus(modal, e);
                }
            }
        });

        // Manage focus for dynamic content
        this.observeDynamicContent();
    }

    trapFocus(container, event) {
        const focusableElements = container.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey) {
            if (document.activeElement === firstElement) {
                lastElement.focus();
                event.preventDefault();
            }
        } else {
            if (document.activeElement === lastElement) {
                firstElement.focus();
                event.preventDefault();
            }
        }
    }

    observeDynamicContent() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType === Node.ELEMENT_NODE) {
                            this.enhanceAccessibility(node);
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Keyboard Navigation
    setupKeyboardNavigation() {
        // Arrow key navigation for custom components
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                const list = e.target.closest('[role="listbox"], [role="menu"]');
                if (list) {
                    this.handleArrowNavigation(list, e);
                }
            }

            // Escape key to close modals
            if (e.key === 'Escape') {
                const modal = document.querySelector('.modal-overlay[aria-hidden="false"]');
                if (modal) {
                    this.closeModal(modal);
                }
            }

            // Enter/Space for custom buttons
            if ((e.key === 'Enter' || e.key === ' ') && e.target.hasAttribute('data-action')) {
                e.preventDefault();
                e.target.click();
            }
        });
    }

    handleArrowNavigation(list, event) {
        const items = Array.from(list.querySelectorAll('[role="option"], [role="menuitem"]'));
        const currentIndex = items.indexOf(document.activeElement);
        let nextIndex;

        if (event.key === 'ArrowDown') {
            nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
        } else {
            nextIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
        }

        items[nextIndex].focus();
        event.preventDefault();
    }

    // ARIA Live Regions
    setupARIALiveRegions() {
        // Create live regions for dynamic content
        const liveRegions = {
            'status': 'polite',
            'alert': 'assertive',
            'log': 'polite'
        };

        Object.entries(liveRegions).forEach(([id, politeness]) => {
            const region = document.createElement('div');
            region.id = `live-${id}`;
            region.setAttribute('aria-live', politeness);
            region.setAttribute('aria-atomic', 'true');
            region.className = 'sr-only';
            document.body.appendChild(region);
        });
    }

    announce(message, type = 'status') {
        const region = document.getElementById(`live-${type}`);
        if (region) {
            region.textContent = message;
            // Clear after announcement
            setTimeout(() => {
                region.textContent = '';
            }, 1000);
        }
    }

    // Form Accessibility
    setupFormAccessibility() {
        // Enhanced form validation
        document.addEventListener('input', (e) => {
            if (e.target.matches('input, textarea, select')) {
                this.validateField(e.target);
            }
        });

        // Form submission feedback
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.hasAttribute('data-validate')) {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.announce('Please correct the errors in the form', 'alert');
                }
            }
        });
    }

    validateField(field) {
        const value = field.value.trim();
        const isRequired = field.hasAttribute('required');
        const type = field.type;

        // Clear previous errors
        this.clearFieldError(field);

        if (isRequired && !value) {
            this.showFieldError(field, 'This field is required');
            return false;
        }

        if (type === 'email' && value && !this.isValidEmail(value)) {
            this.showFieldError(field, 'Please enter a valid email address');
            return false;
        }

        if (type === 'tel' && value && !this.isValidPhone(value)) {
            this.showFieldError(field, 'Please enter a valid phone number');
            return false;
        }

        return true;
    }

    showFieldError(field, message) {
        field.classList.add('error-state');
        field.setAttribute('aria-invalid', 'true');
        
        let errorElement = field.parentNode.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            errorElement.setAttribute('role', 'alert');
            field.parentNode.appendChild(errorElement);
        }
        errorElement.textContent = message;
    }

    clearFieldError(field) {
        field.classList.remove('error-state');
        field.removeAttribute('aria-invalid');
        const errorElement = field.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }

    validateForm(form) {
        const fields = form.querySelectorAll('input, textarea, select');
        let isValid = true;

        fields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    isValidPhone(phone) {
        return /^[\+]?[1-9][\d]{0,15}$/.test(phone.replace(/\s/g, ''));
    }

    // Modal Accessibility
    setupModalAccessibility() {
        document.addEventListener('click', (e) => {
            if (e.target.hasAttribute('data-modal-toggle')) {
                const modalId = e.target.getAttribute('data-modal-toggle');
                const modal = document.getElementById(modalId);
                if (modal) {
                    this.openModal(modal);
                }
            }

            if (e.target.hasAttribute('data-modal-close')) {
                const modal = e.target.closest('.modal-overlay');
                if (modal) {
                    this.closeModal(modal);
                }
            }
        });
    }

    openModal(modal) {
        modal.setAttribute('aria-hidden', 'false');
        modal.style.display = 'flex';
        
        // Focus first focusable element
        const firstFocusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }

        // Announce modal opening
        this.announce('Modal opened', 'status');
    }

    closeModal(modal) {
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        
        // Return focus to trigger
        const trigger = document.querySelector(`[data-modal-toggle="${modal.id}"]`);
        if (trigger) {
            trigger.focus();
        }

        // Announce modal closing
        this.announce('Modal closed', 'status');
    }

    // Table Accessibility
    setupTableAccessibility() {
        // Add table captions if missing
        document.querySelectorAll('table:not([aria-label]):not([aria-labelledby])').forEach(table => {
            if (!table.querySelector('caption')) {
                const caption = document.createElement('caption');
                caption.textContent = 'Data table';
                table.insertBefore(caption, table.firstChild);
            }
        });

        // Add row headers for complex tables
        document.querySelectorAll('table').forEach(table => {
            const rows = table.querySelectorAll('tr');
            rows.forEach((row, index) => {
                if (index === 0) {
                    // Header row
                    row.setAttribute('role', 'row');
                    row.querySelectorAll('th, td').forEach(cell => {
                        cell.setAttribute('role', 'columnheader');
                    });
                } else {
                    // Data rows
                    row.setAttribute('role', 'row');
                    row.querySelectorAll('td').forEach(cell => {
                        cell.setAttribute('role', 'gridcell');
                    });
                }
            });
        });
    }

    // Color Contrast
    setupColorContrast() {
        // Check for high contrast mode
        if (window.matchMedia('(prefers-contrast: high)').matches) {
            document.body.classList.add('high-contrast');
        }

        // Monitor contrast changes
        window.matchMedia('(prefers-contrast: high)').addEventListener('change', (e) => {
            if (e.matches) {
                document.body.classList.add('high-contrast');
            } else {
                document.body.classList.remove('high-contrast');
            }
        });
    }

    // Screen Reader Support
    setupScreenReaderSupport() {
        // Add screen reader text for icons
        document.querySelectorAll('svg[aria-hidden="true"]').forEach(icon => {
            const parent = icon.parentElement;
            if (parent && !parent.getAttribute('aria-label') && !parent.querySelector('.sr-only')) {
                const text = this.getIconDescription(icon);
                if (text) {
                    const srText = document.createElement('span');
                    srText.className = 'sr-only';
                    srText.textContent = text;
                    parent.appendChild(srText);
                }
            }
        });

        // Enhance button descriptions
        document.querySelectorAll('button:not([aria-label])').forEach(button => {
            if (button.textContent.trim() === '') {
                const icon = button.querySelector('svg');
                if (icon) {
                    const description = this.getIconDescription(icon);
                    if (description) {
                        button.setAttribute('aria-label', description);
                    }
                }
            }
        });
    }

    getIconDescription(icon) {
        const iconMap = {
            'M12 6v6m0 0v6m0-6h6m-6 0H6': 'Add',
            'M15 19l-7-7 7-7': 'Back',
            'M6 18L18 6M6 6l12 12': 'Close',
            'M5 13l4 4L19 7': 'Check',
            'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z': 'Time',
            'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z': 'User'
        };

        const path = icon.querySelector('path');
        if (path) {
            const pathData = path.getAttribute('d');
            return iconMap[pathData] || 'Icon';
        }
        return null;
    }

    // Reduced Motion
    setupReducedMotion() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.body.classList.add('reduced-motion');
        }

        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', (e) => {
            if (e.matches) {
                document.body.classList.add('reduced-motion');
            } else {
                document.body.classList.remove('reduced-motion');
            }
        });
    }

    // Utility Methods
    enhanceAccessibility(element) {
        // Add ARIA labels where missing
        if (element.matches('button:not([aria-label])') && element.textContent.trim() === '') {
            const icon = element.querySelector('svg');
            if (icon) {
                const description = this.getIconDescription(icon);
                if (description) {
                    element.setAttribute('aria-label', description);
                }
            }
        }

        // Ensure form inputs have labels
        if (element.matches('input, textarea, select')) {
            if (!element.id || !document.querySelector(`label[for="${element.id}"]`)) {
                const label = element.getAttribute('placeholder') || element.getAttribute('aria-label');
                if (label) {
                    element.setAttribute('aria-label', label);
                }
            }
        }

        // Add role attributes where needed
        if (element.matches('.nav-item')) {
            element.setAttribute('role', 'menuitem');
        }

        if (element.matches('.dropdown')) {
            element.setAttribute('role', 'menu');
        }
    }

    // Public API
    announceToScreenReader(message, type = 'status') {
        this.announce(message, type);
    }

    setFocus(element) {
        if (element) {
            element.focus();
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const isHidden = modal.getAttribute('aria-hidden') === 'true';
            if (isHidden) {
                this.openModal(modal);
            } else {
                this.closeModal(modal);
            }
        }
    }
}

// Initialize Accessibility Manager when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.accessibilityManager = new AccessibilityManager();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AccessibilityManager;
}
