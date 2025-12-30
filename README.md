# Filament Push Notifications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xentixar/filament-push-notifications.svg)](https://packagist.org/packages/xentixar/filament-push-notifications)
[![Total Downloads](https://img.shields.io/packagist/dt/xentixar/filament-push-notifications.svg)](https://packagist.org/packages/xentixar/filament-push-notifications)
[![License](https://img.shields.io/packagist/l/xentixar/filament-push-notifications.svg)](https://packagist.org/packages/xentixar/filament-push-notifications)

A comprehensive Laravel package that provides real-time push notifications for Filament applications. Native notifications use the Web Push API with service workers for persistent delivery, while local notifications provide in-app toast-style alerts. Built with WebSocket technology for instant real-time delivery.

## ✨ Features

- **Real-time Notifications**: Instant push notifications using WebSocket technology
- **Web Push API**: Native notifications use service workers for persistent delivery (even when browser is closed)
- **Dual Notification Types**: Support for both native (web push) and in-app local notifications
- **Scheduled Notifications**: Schedule notifications to be sent at specific times
- **User Targeting**: Send notifications to specific users or groups
- **Filament Admin Panel**: Complete admin interface for managing notifications
- **Service Worker Integration**: Automatic service worker registration and management
- **Push Subscription Management**: Easy subscribe/unsubscribe with visual UI toggle
- **WebSocket Integration**: Built-in WebSocket server using Sockeon for real-time delivery
- **Queue Support**: Background job processing for better performance
- **VAPID Authentication**: Secure web push with VAPID keys
- **Customizable Configuration**: Extensive configuration options for all aspects
- **Migration Ready**: Automatic database setup and migrations

## � Documentation

- [Installation Guide](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [**Upgrading from 1.x to 2.x**](UPGRADE.md) ⬆️

## �🚀 Installation

### Prerequisites

- Laravel 11.x or higher
- PHP 8.2 or higher
- Filament 4.x
- Composer

### Step 1: Install the Package

```bash
composer require xentixar/filament-push-notifications
```

### Step 2: Publish Configuration and Migrations

```bash
php artisan vendor:publish --tag=filament-push-notifications-config
php artisan vendor:publish --tag=filament-push-notifications-migrations
```

### Step 3: Run Migrations

```bash
php artisan migrate
```

### Step 4: Add Plugin to Admin Panel Provider

Add the push notifications plugin to your `app/Providers/Filament/AdminPanelProvider.php`:

```php
use Xentixar\FilamentPushNotifications\PushNotification;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            // ... other configuration
            ->plugins([
                // ... other plugins
                PushNotification::make(),
            ]);
    }
}
```

### Step 5: Generate VAPID Keys

Generate VAPID keys for web push notifications:

```bash
php artisan push:generate-vapid-keys --update-env
```

This will generate and add the VAPID keys to your `.env` file automatically.

### Step 6: Publish Service Worker

Publish the service worker to your public directory:

```bash
php artisan vendor:publish --tag=filament-push-notifications-assets
```

### Step 7: Configure Environment Variables

Add the following variables to your `.env` file:

```env
# WebSocket Server Configuration
SOCKEON_HOST=localhost
SOCKEON_PORT=8080
SOCKEON_KEY=your-secret-key
SOCKEON_DEBUG=true

# Web Push VAPID Keys (Generated via: php artisan push:generate-vapid-keys)
VAPID_PUBLIC_KEY="your-public-key"
VAPID_PRIVATE_KEY="your-private-key"
VAPID_SUBJECT="mailto:admin@example.com"

# Web Push Configuration (Optional)
WEB_PUSH_TTL=2419200
WEB_PUSH_URGENCY=normal

# Native Notification Configuration (Optional)
NOTIFICATION_FAVICON=https://your-domain.com/favicon.ico
NOTIFICATION_DEFAULT_URL=https://your-domain.com
NOTIFICATION_TAG=default
NOTIFICATION_REQUIRE_INTERACTION=false
NOTIFICATION_SILENT=false
NOTIFICATION_BADGE=https://your-domain.com/badge.ico
NOTIFICATION_DIR=auto
NOTIFICATION_LANG=en
NOTIFICATION_RENOTIFY=false
NOTIFICATION_TIMEOUT=5000
```

## 🔧 Configuration

The package configuration file is located at `config/filament-push-notifications.php`. You can customize:
- Web Push settings (VAPID keys, TTL, urgency)
- WebSocket server settings
- Native notification options (icons, badges, vibration patterns, etc.)

Refer to the config file for all available options.

## 📱 Usage

### Starting the WebSocket Server

```bash
php artisan start:sockeon
```

## 🎯 Notification Types

### Native Notifications (Web Push)

Native notifications use the Web Push API with service workers for persistent delivery:
- **Persistent**: Work even when the browser is closed
- **Service Worker**: Automatic registration and management
- **VAPID Authentication**: Secure delivery with VAPID keys
- **Subscription Management**: Easy subscribe/unsubscribe with UI toggle
- **Custom icons and badges**: Fully customizable appearance
- **Click actions**: Navigate to URLs on notification click
- **Vibration patterns**: Mobile device support
- **Rich options**: Sound, interaction requirements, and more

Users must subscribe to web push notifications by clicking the "Enable Web Push" button that appears in the bottom-right corner of the Filament admin panel.

### Local Notifications (In-App)

In-app notifications that appear within the Filament admin panel:
- Toast-style notifications
- Customizable appearance
- Auto-dismiss functionality
- Progress indicators
- Dark mode support

## 🚀 Production Deployment

### SSL/WSS Support

For production HTTPS sites, you need to configure WSS (secure WebSocket). Sockeon supports this through a reverse proxy like Nginx.

**1. Create a Subdomain (Recommended)**
Create a subdomain for your WebSocket server (e.g., `ws.your-domain.com`).

**2. Configure Environment Variables**
Update your `.env` file to separate the bind address from the public address:

```env
# Server Binding (Internal)
SOCKEON_HOST=127.0.0.1
SOCKEON_PORT=8080

# Public Connection (External)
SOCKEON_EXTERNAL_HOST=ws.your-domain.com
SOCKEON_EXTERNAL_PORT=443
```

**3. Configure Nginx**
Point the subdomain to your internal Sockeon server:

```nginx
server {
    listen 443 ssl http2;
    server_name ws.your-domain.com;
    
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;
    
    # Proxy to Sockeon (Internal)
    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        
        # WebSocket support
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        
        # Forward headers
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        
        # WebSocket timeouts
        proxy_connect_timeout 7d;
        proxy_send_timeout 7d;
        proxy_read_timeout 7d;
    }
}
```

**Important Notes:**
- The frontend JavaScript automatically detects HTTPS and uses `wss://` protocol
- Sockeon runs on HTTP internally (e.g., `127.0.0.1:8080`)
- Nginx handles SSL termination and proxies to Sockeon
- Web Push notifications require HTTPS in production

For more details, see [Sockeon Reverse Proxy Documentation](https://sockeon.com/v2.0/advanced/reverse-proxy).

## 🔧 Troubleshooting

### Common Issues

1. **Web Push Not Working**
   - Ensure HTTPS is enabled (required for web push in production)
   - Check that VAPID keys are properly configured
   - Verify service worker is registered (check DevTools → Application → Service Workers)
   - Ensure user has granted notification permission

2. **WebSocket Connection Failed**
   - Ensure the Sockeon server is running: `php artisan sockeon:start`
   - Check host and port configuration in `.env`
   - Verify firewall settings

3. **Notifications Not Appearing**
   - Check browser console for errors
   - Verify user is subscribed to web push
   - Check WebSocket connection status

### Debug Mode

Enable debug mode in your `.env` file:

```env
SOCKEON_DEBUG=true
```

## 🤝 Contributing

We welcome contributions! Please feel free to submit a Pull Request.

## 📄 License

This package is open-sourced software licensed under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- [Filament](https://filamentphp.com/) for the amazing admin panel framework
- [Sockeon](https://sockeon.com) for WebSocket server implementation
- [minishlink/web-push](https://github.com/web-push-libs/web-push-php) for Web Push protocol implementation
- [Laravel](https://laravel.com/) for the robust PHP framework

## 📞 Support

- **Documentation**: [GitHub Wiki](https://github.com/xentixar/filament-push-notifications/wiki)
- **Issues**: [GitHub Issues](https://github.com/xentixar/filament-push-notifications/issues)
- **Discussions**: [GitHub Discussions](https://github.com/xentixar/filament-push-notifications/discussions)
- **Email**: xentixar@gmail.com

---

**Made with ❤️ by [xentixar](https://github.com/xentixar)**