// Service Worker for Beauty Salon Management System

const CACHE_NAME = 'beauty-salon-v1';
const STATIC_CACHE = 'beauty-salon-static-v1';
const DYNAMIC_CACHE = 'beauty-salon-dynamic-v1';

// Assets to cache immediately
const STATIC_ASSETS = [
    '/',
    '/css/app.css',
    '/css/mobile-enhancements.css',
    '/css/accessibility.css',
    '/js/app.js',
    '/js/mobile-ux.js',
    '/js/accessibility.js',
    '/js/performance-optimizer.js',
    '/fonts/inter.woff2',
    '/images/logo.png',
    '/images/hero-bg.jpg'
];

// API endpoints to cache
const API_CACHE_PATTERNS = [
    '/api/services',
    '/api/staff',
    '/api/appointments',
    '/api/clients'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('Service Worker installing...');
    
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('Caching static assets');
                return cache.addAll(STATIC_ASSETS);
            })
            .then(() => {
                console.log('Static assets cached successfully');
                return self.skipWaiting();
            })
            .catch(error => {
                console.error('Failed to cache static assets:', error);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    console.log('Service Worker activating...');
    
    event.waitUntil(
        caches.keys()
            .then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => {
                        if (cacheName !== STATIC_CACHE && cacheName !== DYNAMIC_CACHE) {
                            console.log('Deleting old cache:', cacheName);
                            return caches.delete(cacheName);
                        }
                    })
                );
            })
            .then(() => {
                console.log('Service Worker activated');
                return self.clients.claim();
            })
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip chrome-extension and other non-http requests
    if (!url.protocol.startsWith('http')) {
        return;
    }
    
    event.respondWith(
        handleRequest(request)
    );
});

async function handleRequest(request) {
    const url = new URL(request.url);
    
    try {
        // Handle API requests
        if (url.pathname.startsWith('/api/')) {
            return await handleApiRequest(request);
        }
        
        // Handle static assets
        if (isStaticAsset(url.pathname)) {
            return await handleStaticAsset(request);
        }
        
        // Handle HTML pages
        if (request.headers.get('accept').includes('text/html')) {
            return await handleHtmlRequest(request);
        }
        
        // Default: try cache first, then network
        return await cacheFirst(request);
        
    } catch (error) {
        console.error('Fetch error:', error);
        return await getOfflineFallback(request);
    }
}

async function handleApiRequest(request) {
    const url = new URL(request.url);
    
    // Check if this API endpoint should be cached
    const shouldCache = API_CACHE_PATTERNS.some(pattern => 
        url.pathname.startsWith(pattern)
    );
    
    if (shouldCache) {
        return await networkFirst(request, DYNAMIC_CACHE);
    } else {
        return await networkOnly(request);
    }
}

async function handleStaticAsset(request) {
    return await cacheFirst(request, STATIC_CACHE);
}

async function handleHtmlRequest(request) {
    // For HTML pages, try network first, then cache
    return await networkFirst(request, DYNAMIC_CACHE);
}

// Cache first strategy
async function cacheFirst(request, cacheName = DYNAMIC_CACHE) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        return await getOfflineFallback(request);
    }
}

// Network first strategy
async function networkFirst(request, cacheName = DYNAMIC_CACHE) {
    try {
        const networkResponse = await fetch(request);
        
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            return cachedResponse;
        }
        
        return await getOfflineFallback(request);
    }
}

// Network only strategy
async function networkOnly(request) {
    return await fetch(request);
}

// Check if asset is static
function isStaticAsset(pathname) {
    const staticExtensions = ['.css', '.js', '.png', '.jpg', '.jpeg', '.gif', '.svg', '.woff', '.woff2', '.ttf', '.eot'];
    return staticExtensions.some(ext => pathname.endsWith(ext));
}

// Get offline fallback
async function getOfflineFallback(request) {
    const url = new URL(request.url);
    
    // For HTML requests, return offline page
    if (request.headers.get('accept').includes('text/html')) {
        const offlinePage = await caches.match('/offline.html');
        if (offlinePage) {
            return offlinePage;
        }
    }
    
    // For API requests, return cached data or empty response
    if (url.pathname.startsWith('/api/')) {
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return empty JSON for API requests
        return new Response(
            JSON.stringify({ error: 'Offline', message: 'No cached data available' }),
            {
                status: 503,
                statusText: 'Service Unavailable',
                headers: { 'Content-Type': 'application/json' }
            }
        );
    }
    
    // For other requests, return a generic offline response
    return new Response(
        'Offline - Content not available',
        {
            status: 503,
            statusText: 'Service Unavailable'
        }
    );
}

// Background sync for offline actions
self.addEventListener('sync', event => {
    console.log('Background sync triggered:', event.tag);
    
    if (event.tag === 'appointment-sync') {
        event.waitUntil(syncAppointments());
    } else if (event.tag === 'client-sync') {
        event.waitUntil(syncClients());
    }
});

async function syncAppointments() {
    try {
        // Get pending appointments from IndexedDB
        const pendingAppointments = await getPendingAppointments();
        
        for (const appointment of pendingAppointments) {
            try {
                const response = await fetch('/api/appointments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${appointment.token}`
                    },
                    body: JSON.stringify(appointment.data)
                });
                
                if (response.ok) {
                    await removePendingAppointment(appointment.id);
                    console.log('Appointment synced:', appointment.id);
                }
            } catch (error) {
                console.error('Failed to sync appointment:', appointment.id, error);
            }
        }
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}

async function syncClients() {
    try {
        // Get pending client updates from IndexedDB
        const pendingClients = await getPendingClients();
        
        for (const client of pendingClients) {
            try {
                const response = await fetch(`/api/clients/${client.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${client.token}`
                    },
                    body: JSON.stringify(client.data)
                });
                
                if (response.ok) {
                    await removePendingClient(client.id);
                    console.log('Client synced:', client.id);
                }
            } catch (error) {
                console.error('Failed to sync client:', client.id, error);
            }
        }
    } catch (error) {
        console.error('Background sync failed:', error);
    }
}

// Push notifications
self.addEventListener('push', event => {
    console.log('Push notification received:', event);
    
    const options = {
        body: event.data ? event.data.text() : 'New notification',
        icon: '/images/icon-192x192.png',
        badge: '/images/badge-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        },
        actions: [
            {
                action: 'explore',
                title: 'View Details',
                icon: '/images/checkmark.png'
            },
            {
                action: 'close',
                title: 'Close',
                icon: '/images/xmark.png'
            }
        ]
    };
    
    event.waitUntil(
        self.registration.showNotification('Beauty Salon', options)
    );
});

// Notification click handling
self.addEventListener('notificationclick', event => {
    console.log('Notification clicked:', event);
    
    event.notification.close();
    
    if (event.action === 'explore') {
        event.waitUntil(
            clients.openWindow('/appointments')
        );
    } else if (event.action === 'close') {
        // Just close the notification
    } else {
        // Default action - open the app
        event.waitUntil(
            clients.openWindow('/')
        );
    }
});

// IndexedDB helpers (placeholder implementations)
async function getPendingAppointments() {
    // Implementation would use IndexedDB
    return [];
}

async function removePendingAppointment(id) {
    // Implementation would use IndexedDB
    console.log('Removing pending appointment:', id);
}

async function getPendingClients() {
    // Implementation would use IndexedDB
    return [];
}

async function removePendingClient(id) {
    // Implementation would use IndexedDB
    console.log('Removing pending client:', id);
}

// Message handling for communication with main thread
self.addEventListener('message', event => {
    console.log('Service Worker received message:', event.data);
    
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    } else if (event.data && event.data.type === 'GET_VERSION') {
        event.ports[0].postMessage({ version: CACHE_NAME });
    } else if (event.data && event.data.type === 'CLEAR_CACHE') {
        event.waitUntil(
            caches.keys().then(cacheNames => {
                return Promise.all(
                    cacheNames.map(cacheName => caches.delete(cacheName))
                );
            })
        );
    }
});

console.log('Service Worker loaded successfully');
