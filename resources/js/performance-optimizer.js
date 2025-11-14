// Performance Optimizer for BookingFlow Management System

class PerformanceOptimizer {
    constructor() {
        this.init();
        this.metrics = {};
        this.observers = [];
    }

    init() {
        this.setupPerformanceMonitoring();
        this.setupLazyLoading();
        this.setupImageOptimization();
        this.setupResourcePreloading();
        this.setupCaching();
        this.setupDebouncing();
        this.setupVirtualScrolling();
        this.setupCodeSplitting();
        this.setupServiceWorker();
    }

    // Performance Monitoring
    setupPerformanceMonitoring() {
        // Core Web Vitals
        this.measureCoreWebVitals();
        
        // Resource timing
        this.measureResourceTiming();
        
        // User timing
        this.measureUserTiming();
        
        // Memory usage
        this.monitorMemoryUsage();
    }

    measureCoreWebVitals() {
        // Largest Contentful Paint (LCP)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            const lastEntry = entries[entries.length - 1];
            this.metrics.lcp = lastEntry.startTime;
            this.reportMetric('LCP', lastEntry.startTime);
        }).observe({ entryTypes: ['largest-contentful-paint'] });

        // First Input Delay (FID)
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            entries.forEach(entry => {
                this.metrics.fid = entry.processingStart - entry.startTime;
                this.reportMetric('FID', this.metrics.fid);
            });
        }).observe({ entryTypes: ['first-input'] });

        // Cumulative Layout Shift (CLS)
        let clsValue = 0;
        new PerformanceObserver((entryList) => {
            const entries = entryList.getEntries();
            entries.forEach(entry => {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            });
            this.metrics.cls = clsValue;
            this.reportMetric('CLS', clsValue);
        }).observe({ entryTypes: ['layout-shift'] });
    }

    measureResourceTiming() {
        window.addEventListener('load', () => {
            const resources = performance.getEntriesByType('resource');
            resources.forEach(resource => {
                const loadTime = resource.responseEnd - resource.startTime;
                if (loadTime > 1000) { // Resources taking more than 1 second
                    console.warn(`Slow resource: ${resource.name} took ${loadTime}ms`);
                }
            });
        });
    }

    measureUserTiming() {
        // Mark important milestones
        this.mark('page-start');
        
        window.addEventListener('load', () => {
            this.mark('page-loaded');
            this.measure('page-load-time', 'page-start', 'page-loaded');
        });

        // Mark Livewire component loads
        document.addEventListener('livewire:load', () => {
            this.mark('livewire-loaded');
        });
    }

    monitorMemoryUsage() {
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                if (memory.usedJSHeapSize > memory.jsHeapSizeLimit * 0.8) {
                    console.warn('High memory usage detected');
                    this.triggerGarbageCollection();
                }
            }, 30000); // Check every 30 seconds
        }
    }

    mark(name) {
        performance.mark(name);
    }

    measure(name, startMark, endMark) {
        try {
            performance.measure(name, startMark, endMark);
            const measure = performance.getEntriesByName(name)[0];
            this.reportMetric(name, measure.duration);
        } catch (e) {
            console.warn(`Could not measure ${name}:`, e);
        }
    }

    reportMetric(name, value) {
        // Send to analytics or monitoring service
        if (window.gtag) {
            gtag('event', 'performance_metric', {
                metric_name: name,
                metric_value: value
            });
        }
        
        // Store locally for debugging
        this.metrics[name.toLowerCase()] = value;
    }

    // Lazy Loading
    setupLazyLoading() {
        // Intersection Observer for lazy loading
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        // Lazy load images
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });

        // Lazy load components
        const componentObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const component = entry.target;
                    this.loadComponent(component);
                    observer.unobserve(component);
                }
            });
        });

        document.querySelectorAll('[data-lazy-component]').forEach(component => {
            componentObserver.observe(component);
        });
    }

    loadComponent(element) {
        const componentName = element.dataset.lazyComponent;
        const script = document.createElement('script');
        script.src = `/js/components/${componentName}.js`;
        script.onload = () => {
            element.classList.add('loaded');
        };
        document.head.appendChild(script);
    }

    // Image Optimization
    setupImageOptimization() {
        // WebP support detection
        const webpSupported = this.supportsWebP();
        
        // Optimize images based on device capabilities
        document.querySelectorAll('img').forEach(img => {
            this.optimizeImage(img, webpSupported);
        });
    }

    supportsWebP() {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }

    optimizeImage(img, webpSupported) {
        const src = img.src;
        if (webpSupported && !src.includes('.webp')) {
            img.src = src.replace(/\.(jpg|jpeg|png)$/i, '.webp');
        }
        
        // Add loading="lazy" for images below the fold
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
    }

    // Resource Preloading
    setupResourcePreloading() {
        // Preload critical resources
        this.preloadResource('/css/app.css', 'style');
        this.preloadResource('/js/app.js', 'script');
        
        // Preload fonts
        this.preloadResource('/fonts/inter.woff2', 'font');
        
        // Preload API endpoints that are likely to be called
        this.preloadResource('/api/services', 'fetch');
        this.preloadResource('/api/staff', 'fetch');
    }

    preloadResource(href, as) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.href = href;
        link.as = as;
        if (as === 'font') {
            link.crossOrigin = 'anonymous';
        }
        document.head.appendChild(link);
    }

    // Caching
    setupCaching() {
        // Implement simple in-memory cache
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutes
        
        // Cache API responses
        this.cacheApiResponses();
        
        // Cache DOM queries
        this.cacheDomQueries();
    }

    cacheApiResponses() {
        const originalFetch = window.fetch;
        window.fetch = async (url, options) => {
            const cacheKey = `${url}-${JSON.stringify(options)}`;
            
            // Check cache first
            if (this.cache.has(cacheKey)) {
                const cached = this.cache.get(cacheKey);
                if (Date.now() - cached.timestamp < this.cacheTimeout) {
                    return Promise.resolve(cached.response.clone());
                }
            }
            
            // Fetch and cache
            const response = await originalFetch(url, options);
            if (response.ok) {
                this.cache.set(cacheKey, {
                    response: response.clone(),
                    timestamp: Date.now()
                });
            }
            
            return response;
        };
    }

    cacheDomQueries() {
        const queryCache = new Map();
        
        // Override querySelector with caching
        const originalQuerySelector = Element.prototype.querySelector;
        Element.prototype.querySelector = function(selector) {
            const cacheKey = `${this.tagName}-${selector}`;
            if (queryCache.has(cacheKey)) {
                return queryCache.get(cacheKey);
            }
            
            const result = originalQuerySelector.call(this, selector);
            if (result) {
                queryCache.set(cacheKey, result);
            }
            return result;
        };
    }

    // Debouncing
    setupDebouncing() {
        // Debounce search inputs
        document.addEventListener('input', this.debounce((e) => {
            if (e.target.matches('[data-search]')) {
                this.performSearch(e.target.value);
            }
        }, 300));

        // Debounce resize events
        window.addEventListener('resize', this.debounce(() => {
            this.handleResize();
        }, 250));

        // Debounce scroll events
        window.addEventListener('scroll', this.debounce(() => {
            this.handleScroll();
        }, 100));
    }

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

    performSearch(query) {
        // Implement search with debouncing
        if (query.length > 2) {
            this.searchServices(query);
        }
    }

    handleResize() {
        // Handle responsive changes
        this.updateViewport();
    }

    handleScroll() {
        // Handle scroll-based optimizations
        this.updateScrollPosition();
    }

    // Virtual Scrolling
    setupVirtualScrolling() {
        const virtualScrollContainers = document.querySelectorAll('[data-virtual-scroll]');
        virtualScrollContainers.forEach(container => {
            this.initVirtualScroll(container);
        });
    }

    initVirtualScroll(container) {
        const itemHeight = parseInt(container.dataset.itemHeight) || 50;
        const visibleItems = Math.ceil(container.clientHeight / itemHeight);
        const buffer = 5;
        
        let scrollTop = 0;
        let startIndex = 0;
        let endIndex = visibleItems + buffer;
        
        const updateVisibleItems = () => {
            const newStartIndex = Math.floor(scrollTop / itemHeight);
            const newEndIndex = Math.min(newStartIndex + visibleItems + buffer, container.children.length);
            
            if (newStartIndex !== startIndex || newEndIndex !== endIndex) {
                startIndex = newStartIndex;
                endIndex = newEndIndex;
                this.renderVisibleItems(container, startIndex, endIndex);
            }
        };
        
        container.addEventListener('scroll', () => {
            scrollTop = container.scrollTop;
            updateVisibleItems();
        });
        
        updateVisibleItems();
    }

    renderVisibleItems(container, startIndex, endIndex) {
        // Implementation depends on data structure
        // This is a placeholder for virtual scrolling logic
    }

    // Code Splitting
    setupCodeSplitting() {
        // Dynamic imports for non-critical features
        this.loadFeatureOnDemand('calendar', () => {
            import('./features/calendar.js');
        });
        
        this.loadFeatureOnDemand('charts', () => {
            import('./features/charts.js');
        });
        
        this.loadFeatureOnDemand('reports', () => {
            import('./features/reports.js');
        });
    }

    loadFeatureOnDemand(featureName, loader) {
        const trigger = document.querySelector(`[data-load-feature="${featureName}"]`);
        if (trigger) {
            trigger.addEventListener('click', () => {
                loader();
            });
        }
    }

    // Service Worker
    setupServiceWorker() {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => {
                    console.log('Service Worker registered:', registration);
                })
                .catch(error => {
                    console.log('Service Worker registration failed:', error);
                });
        }
    }

    // Performance Utilities
    triggerGarbageCollection() {
        if (window.gc) {
            window.gc();
        }
    }

    updateViewport() {
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
    }

    updateScrollPosition() {
        const scrollTop = window.pageYOffset;
        document.documentElement.style.setProperty('--scroll-top', `${scrollTop}px`);
    }

    searchServices(query) {
        // Implement service search
        fetch(`/api/services/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                this.updateSearchResults(data);
            });
    }

    updateSearchResults(results) {
        const container = document.querySelector('[data-search-results]');
        if (container) {
            container.innerHTML = results.map(result => 
                `<div class="search-result">${result.name}</div>`
            ).join('');
        }
    }

    // Public API
    getMetrics() {
        return this.metrics;
    }

    clearCache() {
        this.cache.clear();
    }

    preloadRoute(route) {
        this.preloadResource(route, 'fetch');
    }

    optimizeImages() {
        document.querySelectorAll('img').forEach(img => {
            this.optimizeImage(img, this.supportsWebP());
        });
    }
}

// Initialize Performance Optimizer when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.performanceOptimizer = new PerformanceOptimizer();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerformanceOptimizer;
}
