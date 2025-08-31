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

    <div class="notification-container" id="notificationContainer"></div>
    
    <script>
        const socket = new WebSocket('ws://{{ config('filament-push-notifications.socket.host') }}:{{ config('filament-push-notifications.socket.port') }}?key={{ config('filament-push-notifications.socket.key') }}');
        let notificationCounter = 0;

        socket.onopen = () => {
            //
        };

        const handleConnected = (data) => {
            fetch('http://{{ config('filament-push-notifications.socket.host') }}:{{ config('filament-push-notifications.socket.port') }}/sockeon/auth', {
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
                title: notificationData.title || '{{ config("filament-push-notifications.browser_notification.defaults.title") }}',
                message: notificationData.message || '{{ config("filament-push-notifications.browser_notification.defaults.message") }}',
                type: notificationData.type || '{{ config("filament-push-notifications.browser_notification.defaults.type") }}',
                ...notificationData
            };
            
            const notificationType = notificationDataWithDefaults.type || 'filament';
            
            if (notificationType === 'browser') {
                showBrowserNotification(notificationDataWithDefaults);
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

        function showBrowserNotification(notificationData) {
            if (!('Notification' in window)) {
                console.log('This browser does not support notifications');
                showFilamentNotification(notificationData);
                return;
            }

            if (Notification.permission === 'denied') {
                console.log('Browser notifications are blocked');
                showFilamentNotification(notificationData);
                return;
            }

            if (Notification.permission === 'default') {
                showFilamentNotification(notificationData);
                return;
            }

            if (Notification.permission === 'granted') {
                createBrowserNotification(notificationData);
            }
        }



        function requestNotificationPermission() {
            if ('Notification' in window) {
                Notification.requestPermission().then(permission => {
                    if (permission === 'granted') {
                        console.log('Browser notifications enabled');
                    } else {
                        console.log('Browser notifications denied');
                    }
                });
            }
        }

        function createBrowserNotification(notificationData) {
            const config = @json(config('filament-push-notifications.browser_notification'));
            
            const notification = new Notification(notificationData.title || 'Notification', {
                body: notificationData.message || '',
                icon: notificationData.icon || config.favicon,
                badge: notificationData.badge || config.badge,
                tag: notificationData.tag || config.tag,
                requireInteraction: notificationData.requireInteraction !== undefined ? notificationData.requireInteraction : config.require_interaction,
                silent: notificationData.silent !== undefined ? notificationData.silent : config.silent,
                vibrate: notificationData.vibrate || config.vibrate,
                dir: notificationData.dir || config.dir,
                lang: notificationData.lang || config.lang,
                renotify: notificationData.renotify !== undefined ? notificationData.renotify : config.renotify
            });

            if (!notificationData.requireInteraction && !config.require_interaction) {
                setTimeout(() => {
                    notification.close();
                }, config.timeout);
            }

            notification.onclick = function() {
                window.focus();
                notification.close();
                
                if (notificationData.url) {
                    window.location.href = notificationData.url;
                } else if (config.url) {
                    window.location.href = config.url;
                }
            };

            notification.onclose = function() {
                console.log('Browser notification closed');
            };
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

        function initializeNotificationPermissions() {
            if ('Notification' in window) {
                if (Notification.permission === 'default') {
                    requestNotificationPermission();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(initializeNotificationPermissions, 1000);
        });

        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                closeAllNotifications();
            }
        });
    </script>
@endif
