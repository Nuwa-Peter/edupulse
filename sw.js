/**
 * EduPulse - Service Worker
 *
 * This file handles caching of application assets for offline use and
 * intercepts network requests.
 */

const CACHE_NAME = 'edupulse-v1';
const URLS_TO_CACHE = [
    '/',
    '/index.php',
    '/assets/css/custom.css',
    '/assets/js/custom.js',
    // Add other core assets here
];

// Install the service worker and cache core assets
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => {
                console.log('Opened cache');
                return cache.addAll(URLS_TO_CACHE);
            })
    );
});

// Intercept fetch requests
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                // Not in cache - fetch from network
                return fetch(event.request);
            })
    );
});
