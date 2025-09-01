<?php

return [
    /**
     * The model to use for the receivers.
     * This model should have the necessary fields to handle push notifications.
     */
    'receiver_model' => \App\Models\User::class,

    /**
     * The socket configuration for real-time communication.
     * These settings control the WebSocket connection to the notification server.
     */
    'socket' => [
        /**
         * The host of the socket server.
         */
        'host' => env('SOCKEON_HOST', 'localhost'),
        
        /**
         * The port of the socket server.
         */
        'port' => env('SOCKEON_PORT', 8080),
        
        /**
         * The key of the socket server.
         */
        'key' => env('SOCKEON_KEY', 'secret'),
        
        /**
         * Whether the socket server is in debug mode.
         */
        'debug' => env('SOCKEON_DEBUG', true),
        
        /**
         * The allowed origins for the socket server.
         */
        'allowed_origins' => ['http://127.0.0.1:8000', 'http://localhost:8000'],
    ],

    /**
     * The navigation configuration for the Filament admin panel.
     * Controls how the push notifications section appears in the admin navigation.
     */
    'navigation' => [
        /**
         * The group of the push notifications section.
         */
        'group' => 'Settings',

        /**
         * The label of the push notifications section.
         */
        'label' => 'Push Notifications',
    ],

    /**
     * Native notification configuration.
     * These settings control the appearance and behavior of native system notifications.
     * Note: title, message, and type are provided by the backend and cannot be overridden here.
     */
    'native_notification' => [
        /**
         * The favicon/icon URL to display in the native notification.
         * This should be a publicly accessible image URL.
         */
        'favicon' => env('NOTIFICATION_FAVICON', 'https://www.google.com/favicon.ico'),
        
        /**
         * Default URL to navigate to when the notification is clicked.
         * Can be overridden by the backend for specific notifications.
         */
        'url' => env('NOTIFICATION_DEFAULT_URL', 'https://www.google.com'),
        
        /**
         * A unique tag for grouping notifications.
         * Notifications with the same tag will replace each other.
         */
        'tag' => env('NOTIFICATION_TAG', 'default'),
        
        /**
         * Whether the notification requires user interaction before closing.
         * If true, the notification will stay open until manually dismissed.
         */
        'require_interaction' => env('NOTIFICATION_REQUIRE_INTERACTION', false),
        
        /**
         * Vibration pattern for mobile devices.
         * Array of numbers representing vibration intervals in milliseconds.
         */
        'vibrate' => [100, 100, 100],
        
        /**
         * Note: Custom actions are only supported for persistent notifications
         * shown through Service Workers, not for regular native notifications.
         * This configuration is kept for future Service Worker implementation.
         */
        'actions' => [
            [
                'action' => 'open_url',
                'title' => 'Open',
                'url' => env('NOTIFICATION_DEFAULT_URL', 'https://www.google.com'),
            ],
            [
                'action' => 'dismiss',
                'title' => 'Dismiss',
            ],
        ],
        
        /**
         * Whether the notification should be silent (no sound).
         * If false, the system will play a default notification sound.
         */
        'silent' => env('NOTIFICATION_SILENT', false),
        
        /**
         * Badge icon URL for the notification.
         * Displayed in the browser tab or app icon.
         */
        'badge' => env('NOTIFICATION_BADGE', 'https://www.google.com/favicon.ico'),
        
        /**
         * Notification direction for RTL languages.
         * Can be 'auto', 'ltr', or 'rtl'.
         */
        'dir' => env('NOTIFICATION_DIR', 'auto'),
        
        /**
         * Language of the notification.
         * Should be a valid BCP 47 language tag.
         */
        'lang' => env('NOTIFICATION_LANG', 'en'),
        
        /**
         * Whether to renotify when a notification with the same tag is shown.
         * If true, the notification will be shown even if one with the same tag exists.
         */
        'renotify' => env('NOTIFICATION_RENOTIFY', false),
        
        /**
         * Default timeout for auto-closing notifications in milliseconds.
         * Only applies when require_interaction is false.
         */
        'timeout' => env('NOTIFICATION_TIMEOUT', 5000),
    ],
];