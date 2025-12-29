@if (auth()->check())
    <style>
        /* Notification Container */
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 400px;
        }

        /* Individual Notification */
        .notification {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }

        .notification.hide {
            transform: translateX(100%);
            opacity: 0;
        }

        /* Notification Icon */
        .notification-icon {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 2px;
            background: #f3f4f6;
            color: #6b7280;
        }

        .notification-icon svg {
            width: 16px;
            height: 16px;
        }

        /* Notification Content */
        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-weight: 600;
            font-size: 14px;
            color: #111827;
            margin: 0 0 4px 0;
            line-height: 1.4;
        }

        .notification-message {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
            line-height: 1.4;
        }

        /* Close Button */
        .notification-close {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .notification-close:hover {
            background: #f3f4f6;
            color: #6b7280;
        }

        .notification-close svg {
            width: 16px;
            height: 16px;
        }

        /* Progress Bar */
        .notification-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: #e5e7eb;
            width: 100%;
            overflow: hidden;
        }

        .notification-progress-bar {
            height: 100%;
            background: #3b82f6;
            width: 100%;
            animation: progress 5s linear forwards;
        }

        @keyframes progress {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .notification-container {
                right: 10px;
                left: 10px;
                max-width: none;
            }

            .notification {
                padding: 14px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .notification {
                background: #1f2937;
                border-color: #374151;
                color: white;
            }

            .notification-title {
                color: #f9fafb;
            }

            .notification-message {
                color: #d1d5db;
            }

            .notification-close:hover {
                background: #374151;
                color: #d1d5db;
            }
        }
    </style>

    <!-- Web Push Subscription Toggle -->
    <div id="webPushToggleContainer" style="position: fixed; bottom: 20px; right: 20px; z-index: 9998; display: none;">
        <button id="webPushToggle" style="
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    border-radius: 50px;
                    padding: 12px 24px;
                    font-size: 14px;
                    font-weight: 600;
                    cursor: pointer;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
                    transition: all 0.3s ease;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                "
            onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(102, 126, 234, 0.6)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(102, 126, 234, 0.4)';">
            <svg id="webPushIcon" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                    d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z" />
            </svg>
            <span id="webPushText">Enable Web Push</span>
        </button>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <script>
        // Push Subscription Manager Class
        class PushSubscriptionManager {
            constructor() {
                this.registration = null;
                this.subscription = null;
                this.publicKey = null;
            }

            async init() {
                if (!('serviceWorker' in navigator)) {
                    console.warn('Service Workers are not supported');
                    return false;
                }

                if (!('PushManager' in window)) {
                    console.warn('Push API is not supported');
                    return false;
                }

                try {
                    this.registration = await this.registerServiceWorker();
                    this.publicKey = await this.getPublicKey();

                    if (!this.publicKey) {
                        console.error('Failed to get VAPID public key');
                        return false;
                    }

                    this.subscription = await this.registration.pushManager.getSubscription();
                    return true;
                } catch (error) {
                    console.error('Failed to initialize push subscription manager:', error);
                    return false;
                }
            }

            async registerServiceWorker() {
                try {
                    const registration = await navigator.serviceWorker.register('/service-worker.js', {
                        scope: '/'
                    });
                    console.log('Service Worker registered successfully:', registration);
                    await navigator.serviceWorker.ready;
                    return registration;
                } catch (error) {
                    console.error('Service Worker registration failed:', error);
                    throw error;
                }
            }

            async getPublicKey() {
                try {
                    const response = await fetch('/push-notifications/vapid-public-key');
                    const data = await response.json();

                    if (data.error) {
                        console.error('Error getting public key:', data.error);
                        return null;
                    }

                    return data.publicKey;
                } catch (error) {
                    console.error('Failed to fetch VAPID public key:', error);
                    return null;
                }
            }

            async requestPermission() {
                if (!('Notification' in window)) {
                    console.warn('Notifications are not supported');
                    return 'denied';
                }

                const permission = await Notification.requestPermission();
                console.log('Notification permission:', permission);
                return permission;
            }

            async subscribe() {
                try {
                    const permission = await this.requestPermission();

                    if (permission !== 'granted') {
                        console.warn('Notification permission denied');
                        return false;
                    }

                    if (this.subscription) {
                        console.log('Already subscribed to push notifications');
                        return true;
                    }

                    const subscription = await this.registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: this.urlBase64ToUint8Array(this.publicKey)
                    });

                    console.log('Push subscription created:', subscription);

                    const success = await this.sendSubscriptionToServer(subscription);

                    if (success) {
                        this.subscription = subscription;
                        return true;
                    }

                    return false;
                } catch (error) {
                    console.error('Failed to subscribe to push notifications:', error);
                    return false;
                }
            }

            async unsubscribe() {
                try {
                    if (!this.subscription) {
                        console.log('No active subscription to unsubscribe from');
                        return true;
                    }

                    const success = await this.subscription.unsubscribe();

                    if (success) {
                        await this.removeSubscriptionFromServer(this.subscription);
                        this.subscription = null;
                        console.log('Successfully unsubscribed from push notifications');
                        return true;
                    }

                    return false;
                } catch (error) {
                    console.error('Failed to unsubscribe from push notifications:', error);
                    return false;
                }
            }

            async sendSubscriptionToServer(subscription) {
                try {
                    const subscriptionJson = subscription.toJSON();

                    const response = await fetch('/push-notifications/subscribe', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify(subscriptionJson)
                    });

                    const data = await response.json();

                    if (data.success) {
                        console.log('Subscription saved to server:', data);
                        return true;
                    } else {
                        console.error('Failed to save subscription:', data);
                        return false;
                    }
                } catch (error) {
                    console.error('Error sending subscription to server:', error);
                    return false;
                }
            }

            async removeSubscriptionFromServer(subscription) {
                try {
                    const subscriptionJson = subscription.toJSON();

                    const response = await fetch('/push-notifications/unsubscribe', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                        },
                        body: JSON.stringify({ endpoint: subscriptionJson.endpoint })
                    });

                    const data = await response.json();
                    console.log('Subscription removed from server:', data);
                    return data.success || false;
                } catch (error) {
                    console.error('Error removing subscription from server:', error);
                    return false;
                }
            }

            isSubscribed() {
                return this.subscription !== null;
            }

            urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/\-/g, '+')
                    .replace(/_/g, '/');

                const rawData = window.atob(base64);
                const outputArray = new Uint8Array(rawData.length);

                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }

                return outputArray;
            }
        }

        // WebSocket and Notification Code
        const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const httpProtocol = window.location.protocol === 'https:' ? 'https:' : 'http:';
        const socketHost = '{{ config('filament-push-notifications.socket.host') }}';
        const socketPort = '{{ config('filament-push-notifications.socket.port') }}';
        const socketKey = '{{ config('filament-push-notifications.socket.key') }}';

        const socket = new WebSocket(
            `${wsProtocol}//${socketHost}:${socketPort}?key=${socketKey}`
        );
        let notificationCounter = 0;

        socket.onopen = () => {
            //
        };

        const handleConnected = (data) => {
            fetch(`${httpProtocol}//${socketHost}:${socketPort}/sockeon/auth`, {
                method: 'POST',
                body: JSON.stringify({
                    clientId: data.clientId,
                    userId: {{ auth()->user()->id }},
                }),
                headers: {
                    'Content-Type': 'application/json',
                },
            }).then(response => response.json()).then(data => {
                //
            });
        };

        function showNotification(notificationData) {
            const notificationDataWithDefaults = {
                title: notificationData.title ||
                    '{{ config('filament-push-notifications.native_notification.defaults.title') }}',
                message: notificationData.message ||
                    '{{ config('filament-push-notifications.native_notification.defaults.message') }}',
                type: notificationData.type ||
                    '{{ config('filament-push-notifications.native_notification.defaults.type') }}',
                ...notificationData
            };

            const notificationType = notificationDataWithDefaults.type || 'local';

            if (notificationType === 'native') {
                showNativeNotification(notificationDataWithDefaults);
            } else {
                showFilamentNotification(notificationDataWithDefaults);
            }
        }

        function showFilamentNotification(notificationData) {
            const container = document.getElementById('notificationContainer');
            const notificationId = `notification-${++notificationCounter}`;

            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.id = notificationId;

            notification.innerHTML = `
                                        <div class="notification-icon">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">${notificationData.title || 'Notification'}</div>
                                            <div class="notification-message">${notificationData.message || ''}</div>
                                        </div>
                                        <button class="notification-close" onclick="closeNotification('${notificationId}')">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                        <div class="notification-progress">
                                            <div class="notification-progress-bar"></div>
                                        </div>
                                    `;

            container.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            setTimeout(() => {
                closeNotification(notificationId);
            }, 5000);
        }

        function showNativeNotification(notificationData) {
            // Native notifications now use web push via service worker
            if (!pushManager || !pushManager.isSubscribed()) {
                console.log('User not subscribed to web push, showing local notification instead');
                showFilamentNotification(notificationData);
                return;
            }

            // For already subscribed users, the notification will come through the service worker
            // This function is called when receiving via WebSocket, so we just show local notification
            // The actual web push will be sent from the backend
            showFilamentNotification(notificationData);
        }



        function getIconSvg(type) {
            const icons = {
                info: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>`,
                success: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                                                        </svg>`,
                error: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                                                        </svg>`
            };

            return icons[type] || icons.info;
        }

        function closeNotification(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.add('hide');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }

        function closeAllNotifications() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                notification.classList.add('hide');
            });

            setTimeout(() => {
                const container = document.getElementById('notificationContainer');
                container.innerHTML = '';
            }, 300);
        }

        socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            switch (data.event) {
                case 'sockeon.connected':
                    handleConnected(data.data);
                    break;
                case 'notification.pushed':
                    showNotification(data.data.notification);
                    break;
                default:
                    break;
            }
        };

        socket.onerror = (event) => {
            console.error('WebSocket error:', event);
        };

        socket.onclose = () => {
            console.log('WebSocket connection closed');
        };


        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initializeWebPush, 1000);
        });

        // Web Push Subscription Management
        let pushManager = null;

        async function initializeWebPush() {
            pushManager = new PushSubscriptionManager();
            const initialized = await pushManager.init();

            if (initialized) {
                updateWebPushButton();
            } else {
                console.warn('Web Push not supported or failed to initialize');
                document.getElementById('webPushToggle').style.display = 'none';
            }
        }

        function updateWebPushButton() {
            const button = document.getElementById('webPushToggle');
            const container = document.getElementById('webPushToggleContainer');
            const text = document.getElementById('webPushText');

            if (pushManager && pushManager.isSubscribed()) {
                // User is subscribed - hide the button
                container.style.display = 'none';
            } else {
                // User is not subscribed - show the button
                container.style.display = 'block';
                text.textContent = 'Enable Web Push';
                button.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
        }

        document.getElementById('webPushToggle')?.addEventListener('click', async function () {
            if (!pushManager) {
                console.error('Push manager not initialized');
                return;
            }

            const button = this;
            const text = document.getElementById('webPushText');
            const container = document.getElementById('webPushToggleContainer');
            const originalText = text.textContent;

            button.disabled = true;
            text.textContent = 'Processing...';

            try {
                if (pushManager.isSubscribed()) {
                    const success = await pushManager.unsubscribe();
                    if (success) {
                        showFilamentNotification({
                            title: 'Web Push Disabled',
                            message: 'You will no longer receive push notifications'
                        });
                        // Show button again after unsubscribe
                        container.style.display = 'block';
                    } else {
                        showFilamentNotification({
                            title: 'Error',
                            message: 'Failed to disable web push notifications'
                        });
                    }
                } else {
                    const success = await pushManager.subscribe();
                    if (success) {
                        showFilamentNotification({
                            title: 'Web Push Enabled',
                            message: 'You will now receive push notifications even when the browser is closed'
                        });
                        // Hide button after successful subscription
                        setTimeout(() => {
                            container.style.display = 'none';
                        }, 2000);
                    } else {
                        showFilamentNotification({
                            title: 'Error',
                            message: 'Failed to enable web push notifications. Please check your browser permissions.'
                        });
                    }
                }

                updateWebPushButton();
            } catch (error) {
                console.error('Error toggling web push:', error);
                showFilamentNotification({
                    title: 'Error',
                    message: 'An error occurred while managing push notifications'
                });
                text.textContent = originalText;
            } finally {
                button.disabled = false;
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                closeAllNotifications();
            }
        });
    </script>
@endif