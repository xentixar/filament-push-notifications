// Service Worker for Web Push Notifications
// This file handles push events and notification clicks

self.addEventListener('install', (event) => {
    console.log('Service Worker installing...');
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    console.log('Service Worker activating...');
    event.waitUntil(clients.claim());
});

// Handle push events
self.addEventListener('push', (event) => {
    console.log('Push event received:', event);

    let notificationData = {
        title: 'New Notification',
        body: 'You have a new notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: 'default',
        requireInteraction: false,
        vibrate: [100, 50, 100],
        data: {}
    };

    // Parse the push payload
    if (event.data) {
        try {
            const payload = event.data.json();
            notificationData = {
                title: payload.title || notificationData.title,
                body: payload.body || notificationData.body,
                icon: payload.icon || notificationData.icon,
                badge: payload.badge || notificationData.badge,
                tag: payload.tag || notificationData.tag,
                requireInteraction: payload.requireInteraction || notificationData.requireInteraction,
                vibrate: payload.vibrate || notificationData.vibrate,
                data: payload.data || notificationData.data,
                actions: payload.actions || []
            };
        } catch (error) {
            console.error('Error parsing push payload:', error);
        }
    }

    const promiseChain = self.registration.showNotification(
        notificationData.title,
        {
            body: notificationData.body,
            icon: notificationData.icon,
            badge: notificationData.badge,
            tag: notificationData.tag,
            requireInteraction: notificationData.requireInteraction,
            vibrate: notificationData.vibrate,
            data: notificationData.data,
            actions: notificationData.actions
        }
    );

    event.waitUntil(promiseChain);
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);

    event.notification.close();

    // Handle action button clicks
    if (event.action) {
        console.log('Action clicked:', event.action);

        // Handle custom actions
        if (event.notification.data && event.notification.data.actions) {
            const action = event.notification.data.actions.find(a => a.action === event.action);
            if (action && action.url) {
                event.waitUntil(
                    clients.openWindow(action.url)
                );
                return;
            }
        }
    }

    // Default click behavior - focus or open window
    const urlToOpen = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((windowClients) => {
                // Check if there's already a window open
                for (let i = 0; i < windowClients.length; i++) {
                    const client = windowClients[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }

                // If no window is open, open a new one
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
});

// Handle notification close
self.addEventListener('notificationclose', (event) => {
    console.log('Notification closed:', event);

    // You can track notification dismissals here if needed
    // For example, send analytics data
});

// Handle messages from the main thread
self.addEventListener('message', (event) => {
    console.log('Service Worker received message:', event.data);

    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
