// Enhanced Error Handling for BookingFlow Management System

class ErrorHandler {
    constructor() {
        this.init();
        this.errorTypes = {
            NETWORK: 'network',
            VALIDATION: 'validation',
            AUTHENTICATION: 'authentication',
            AUTHORIZATION: 'authorization',
            SERVER: 'server',
            CLIENT: 'client',
            UNKNOWN: 'unknown'
        };
    }

    init() {
        this.setupGlobalErrorHandling();
        this.setupNetworkErrorHandling();
        this.setupValidationErrorHandling();
        this.setupAuthenticationErrorHandling();
        this.setupLivewireErrorHandling();
        this.setupUserFeedback();
        this.setupErrorReporting();
    }

    // Global Error Handling
    setupGlobalErrorHandling() {
        // Unhandled JavaScript errors
        window.addEventListener('error', (event) => {
            this.handleError({
                type: this.errorTypes.CLIENT,
                message: event.message,
                filename: event.filename,
                lineno: event.lineno,
                colno: event.colno,
                error: event.error
            });
        });

        // Unhandled promise rejections
        window.addEventListener('unhandledrejection', (event) => {
            this.handleError({
                type: this.errorTypes.CLIENT,
                message: event.reason?.message || 'Unhandled promise rejection',
                error: event.reason
            });
        });
    }

    // Network Error Handling
    setupNetworkErrorHandling() {
        // Override fetch to handle network errors
        const originalFetch = window.fetch;
        window.fetch = async (url, options) => {
            try {
                const response = await originalFetch(url, options);
                
                if (!response.ok) {
                    await this.handleHttpError(response, url, options);
                }
                
                return response;
            } catch (error) {
                if (error.name === 'TypeError' && error.message.includes('fetch')) {
                    this.handleError({
                        type: this.errorTypes.NETWORK,
                        message: 'Network connection failed',
                        url: url,
                        error: error
                    });
                }
                throw error;
            }
        };
    }

    async handleHttpError(response, url, options) {
        const errorData = await this.parseErrorResponse(response);
        
        let errorType = this.errorTypes.SERVER;
        if (response.status === 401) {
            errorType = this.errorTypes.AUTHENTICATION;
        } else if (response.status === 403) {
            errorType = this.errorTypes.AUTHORIZATION;
        } else if (response.status >= 400 && response.status < 500) {
            errorType = this.errorTypes.CLIENT;
        }

        this.handleError({
            type: errorType,
            message: errorData.message || `HTTP ${response.status} Error`,
            status: response.status,
            url: url,
            data: errorData
        });
    }

    async parseErrorResponse(response) {
        try {
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return { message: await response.text() };
            }
        } catch (error) {
            return { message: 'Failed to parse error response' };
        }
    }

    // Validation Error Handling
    setupValidationErrorHandling() {
        // Handle form validation errors
        document.addEventListener('invalid', (event) => {
            if (event.target.matches('input, textarea, select')) {
                this.handleValidationError(event.target);
            }
        });

        // Handle custom validation
        document.addEventListener('validation-error', (event) => {
            this.handleError({
                type: this.errorTypes.VALIDATION,
                message: event.detail.message,
                field: event.detail.field,
                errors: event.detail.errors
            });
        });
    }

    handleValidationError(field) {
        const message = this.getValidationMessage(field);
        this.showFieldError(field, message);
    }

    getValidationMessage(field) {
        if (field.validity.valueMissing) {
            return `${this.getFieldLabel(field)} is required`;
        }
        if (field.validity.typeMismatch) {
            return `Please enter a valid ${field.type}`;
        }
        if (field.validity.patternMismatch) {
            return `Please enter a valid ${this.getFieldLabel(field).toLowerCase()}`;
        }
        if (field.validity.tooShort) {
            return `${this.getFieldLabel(field)} must be at least ${field.minLength} characters`;
        }
        if (field.validity.tooLong) {
            return `${this.getFieldLabel(field)} must be no more than ${field.maxLength} characters`;
        }
        if (field.validity.rangeUnderflow) {
            return `${this.getFieldLabel(field)} must be at least ${field.min}`;
        }
        if (field.validity.rangeOverflow) {
            return `${this.getFieldLabel(field)} must be no more than ${field.max}`;
        }
        return 'Please enter a valid value';
    }

    getFieldLabel(field) {
        const label = document.querySelector(`label[for="${field.id}"]`);
        if (label) {
            return label.textContent.replace('*', '').trim();
        }
        return field.placeholder || field.name || 'Field';
    }

    // Authentication Error Handling
    setupAuthenticationErrorHandling() {
        // Handle 401 errors globally
        document.addEventListener('auth-error', (event) => {
            this.handleError({
                type: this.errorTypes.AUTHENTICATION,
                message: 'Your session has expired. Please log in again.',
                action: 'redirect',
                url: '/login'
            });
        });

        // Handle token expiration
        document.addEventListener('token-expired', (event) => {
            this.showError({
                title: 'Session Expired',
                message: 'Your session has expired. Please log in again.',
                type: 'warning',
                actions: [
                    {
                        text: 'Log In',
                        action: () => window.location.href = '/login'
                    }
                ]
            });
        });
    }

    // Livewire Error Handling
    setupLivewireErrorHandling() {
        // Handle Livewire errors
        document.addEventListener('livewire:load', () => {
            Livewire.on('error', (error) => {
                this.handleError({
                    type: this.errorTypes.CLIENT,
                    message: error.message || 'An error occurred',
                    component: error.component,
                    error: error
                });
            });

            Livewire.on('validation-error', (errors) => {
                this.handleValidationErrors(errors);
            });
        });
    }

    handleValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const fieldElement = document.querySelector(`[wire\\:model="${field}"]`);
            if (fieldElement) {
                this.showFieldError(fieldElement, errors[field][0]);
            }
        });
    }

    // User Feedback
    setupUserFeedback() {
        this.createNotificationContainer();
    }

    createNotificationContainer() {
        const container = document.createElement('div');
        container.id = 'error-notifications';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
    }

    // Main Error Handler
    handleError(error) {
        console.error('Error handled:', error);
        
        // Log error for debugging
        this.logError(error);
        
        // Show user-friendly message
        this.showUserFriendlyError(error);
        
        // Report error if needed
        this.reportError(error);
        
        // Handle specific error types
        this.handleErrorType(error);
    }

    showUserFriendlyError(error) {
        const userMessage = this.getUserFriendlyMessage(error);
        
        this.showError({
            title: this.getErrorTitle(error.type),
            message: userMessage,
            type: this.getErrorType(error.type),
            actions: this.getErrorActions(error)
        });
    }

    getUserFriendlyMessage(error) {
        const messages = {
            [this.errorTypes.NETWORK]: 'Unable to connect to the server. Please check your internet connection and try again.',
            [this.errorTypes.VALIDATION]: 'Please check your input and try again.',
            [this.errorTypes.AUTHENTICATION]: 'Please log in to continue.',
            [this.errorTypes.AUTHORIZATION]: 'You do not have permission to perform this action.',
            [this.errorTypes.SERVER]: 'The server is experiencing issues. Please try again later.',
            [this.errorTypes.CLIENT]: 'Something went wrong. Please refresh the page and try again.',
            [this.errorTypes.UNKNOWN]: 'An unexpected error occurred. Please try again.'
        };

        return error.message || messages[error.type] || messages[this.errorTypes.UNKNOWN];
    }

    getErrorTitle(type) {
        const titles = {
            [this.errorTypes.NETWORK]: 'Connection Error',
            [this.errorTypes.VALIDATION]: 'Validation Error',
            [this.errorTypes.AUTHENTICATION]: 'Authentication Required',
            [this.errorTypes.AUTHORIZATION]: 'Access Denied',
            [this.errorTypes.SERVER]: 'Server Error',
            [this.errorTypes.CLIENT]: 'Error',
            [this.errorTypes.UNKNOWN]: 'Unexpected Error'
        };

        return titles[type] || 'Error';
    }

    getErrorType(type) {
        const types = {
            [this.errorTypes.NETWORK]: 'warning',
            [this.errorTypes.VALIDATION]: 'error',
            [this.errorTypes.AUTHENTICATION]: 'warning',
            [this.errorTypes.AUTHORIZATION]: 'error',
            [this.errorTypes.SERVER]: 'error',
            [this.errorTypes.CLIENT]: 'error',
            [this.errorTypes.UNKNOWN]: 'error'
        };

        return types[type] || 'error';
    }

    getErrorActions(error) {
        const actions = [];

        if (error.type === this.errorTypes.NETWORK) {
            actions.push({
                text: 'Retry',
                action: () => this.retryLastAction()
            });
        }

        if (error.type === this.errorTypes.AUTHENTICATION) {
            actions.push({
                text: 'Log In',
                action: () => window.location.href = '/login'
            });
        }

        if (error.action === 'redirect' && error.url) {
            actions.push({
                text: 'Continue',
                action: () => window.location.href = error.url
            });
        }

        return actions;
    }

    // Error Display
    showError({ title, message, type = 'error', actions = [], duration = 5000 }) {
        const notification = document.createElement('div');
        notification.className = `error-notification error-notification-${type}`;
        notification.innerHTML = this.createNotificationHTML(title, message, actions);

        const container = document.getElementById('error-notifications');
        container.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);

        // Auto-dismiss
        if (duration > 0) {
            setTimeout(() => {
                this.dismissNotification(notification);
            }, duration);
        }

        // Add action handlers
        actions.forEach((action, index) => {
            const button = notification.querySelector(`[data-action="${index}"]`);
            if (button) {
                button.addEventListener('click', action.action);
            }
        });
    }

    createNotificationHTML(title, message, actions) {
        const icon = this.getNotificationIcon(type);
        const actionsHTML = actions.map((action, index) => 
            `<button class="error-action-btn" data-action="${index}">${action.text}</button>`
        ).join('');

        return `
            <div class="error-notification-content">
                <div class="error-notification-header">
                    <div class="error-notification-icon">${icon}</div>
                    <div class="error-notification-title">${title}</div>
                    <button class="error-notification-close" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="error-notification-message">${message}</div>
                ${actions.length > 0 ? `<div class="error-notification-actions">${actionsHTML}</div>` : ''}
            </div>
        `;
    }

    getNotificationIcon(type) {
        const icons = {
            error: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
            warning: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
            success: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
            info: '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
        };

        return icons[type] || icons.error;
    }

    dismissNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // Field Error Display
    showFieldError(field, message) {
        // Remove existing error
        this.clearFieldError(field);

        // Add error class
        field.classList.add('error-state');
        field.setAttribute('aria-invalid', 'true');

        // Create error message
        const errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        errorElement.setAttribute('role', 'alert');
        errorElement.textContent = message;

        // Insert after field
        field.parentNode.insertBefore(errorElement, field.nextSibling);

        // Focus field
        field.focus();

        // Announce to screen readers
        if (window.accessibilityManager) {
            window.accessibilityManager.announceToScreenReader(message, 'alert');
        }
    }

    clearFieldError(field) {
        field.classList.remove('error-state');
        field.removeAttribute('aria-invalid');
        
        const errorElement = field.parentNode.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Error Reporting
    setupErrorReporting() {
        // Report errors to monitoring service
        this.errorQueue = [];
        this.reportInterval = setInterval(() => {
            this.flushErrorQueue();
        }, 30000); // Report every 30 seconds
    }

    reportError(error) {
        // Add to queue for batch reporting
        this.errorQueue.push({
            ...error,
            timestamp: Date.now(),
            userAgent: navigator.userAgent,
            url: window.location.href,
            userId: this.getCurrentUserId()
        });
    }

    async flushErrorQueue() {
        if (this.errorQueue.length === 0) return;

        const errors = [...this.errorQueue];
        this.errorQueue = [];

        try {
            await fetch('/api/errors/report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.getAuthToken()}`
                },
                body: JSON.stringify({ errors })
            });
        } catch (error) {
            console.error('Failed to report errors:', error);
            // Re-queue errors for next attempt
            this.errorQueue.unshift(...errors);
        }
    }

    // Error Type Handling
    handleErrorType(error) {
        switch (error.type) {
            case this.errorTypes.NETWORK:
                this.handleNetworkError(error);
                break;
            case this.errorTypes.AUTHENTICATION:
                this.handleAuthenticationError(error);
                break;
            case this.errorTypes.AUTHORIZATION:
                this.handleAuthorizationError(error);
                break;
            case this.errorTypes.SERVER:
                this.handleServerError(error);
                break;
        }
    }

    handleNetworkError(error) {
        // Show offline indicator
        this.showOfflineIndicator();
        
        // Queue actions for when connection is restored
        this.queueOfflineAction(error);
    }

    handleAuthenticationError(error) {
        // Clear auth tokens
        localStorage.removeItem('auth_token');
        sessionStorage.removeItem('auth_token');
        
        // Redirect to login
        setTimeout(() => {
            window.location.href = '/login';
        }, 2000);
    }

    handleAuthorizationError(error) {
        // Show access denied message
        this.showError({
            title: 'Access Denied',
            message: 'You do not have permission to perform this action.',
            type: 'error',
            duration: 0
        });
    }

    handleServerError(error) {
        // Show server error message
        this.showError({
            title: 'Server Error',
            message: 'The server is experiencing issues. Please try again later.',
            type: 'error',
            actions: [
                {
                    text: 'Retry',
                    action: () => window.location.reload()
                }
            ]
        });
    }

    // Utility Methods
    logError(error) {
        console.group('Error Details');
        console.error('Type:', error.type);
        console.error('Message:', error.message);
        console.error('Error:', error.error);
        console.error('Stack:', error.error?.stack);
        console.groupEnd();
    }

    getCurrentUserId() {
        // Get current user ID from auth state
        return localStorage.getItem('user_id') || 'anonymous';
    }

    getAuthToken() {
        // Get auth token from storage
        return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    }

    retryLastAction() {
        // Implement retry logic for last failed action
        if (this.lastAction) {
            this.lastAction();
        }
    }

    showOfflineIndicator() {
        // Show offline indicator
        const indicator = document.createElement('div');
        indicator.id = 'offline-indicator';
        indicator.className = 'fixed bottom-4 left-4 bg-yellow-500 text-white px-4 py-2 rounded-lg shadow-lg';
        indicator.textContent = 'You are offline. Some features may not be available.';
        document.body.appendChild(indicator);
    }

    queueOfflineAction(error) {
        // Queue action for when connection is restored
        if (!this.offlineQueue) {
            this.offlineQueue = [];
        }
        this.offlineQueue.push(error);
    }

    // Public API
    showSuccess(message, title = 'Success') {
        this.showError({
            title,
            message,
            type: 'success',
            duration: 3000
        });
    }

    showWarning(message, title = 'Warning') {
        this.showError({
            title,
            message,
            type: 'warning',
            duration: 5000
        });
    }

    showInfo(message, title = 'Information') {
        this.showError({
            title,
            message,
            type: 'info',
            duration: 4000
        });
    }

    clearAllErrors() {
        const container = document.getElementById('error-notifications');
        if (container) {
            container.innerHTML = '';
        }
    }
}

// Initialize Error Handler when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.errorHandler = new ErrorHandler();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ErrorHandler;
}
