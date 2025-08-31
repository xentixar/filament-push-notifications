# Filament Push Notifications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xentixar/filament-push-notifications.svg)](https://packagist.org/packages/xentixar/filament-push-notifications)
[![Total Downloads](https://img.shields.io/packagist/dt/xentixar/filament-push-notifications.svg)](https://packagist.org/packages/xentixar/filament-push-notifications)
[![License](https://img.shields.io/packagist/l/xentixar/filament-push-notifications.svg)](https://packagist.org/packages/xentixar/filament-push-notifications)

A comprehensive Laravel package that provides real-time push notifications for Filament applications with support for both browser notifications and in-app Filament notifications. Built with WebSocket technology for instant delivery and seamless user experience.

## ✨ Features

- **Real-time Notifications**: Instant push notifications using WebSocket technology
- **Dual Notification Types**: Support for both browser notifications and in-app Filament notifications
- **Scheduled Notifications**: Schedule notifications to be sent at specific times
- **User Targeting**: Send notifications to specific users or groups
- **Filament Admin Panel**: Complete admin interface for managing notifications
- **Browser Notification Support**: Native browser notifications with customizable options
- **WebSocket Integration**: Built-in WebSocket server using Sockeon
- **Queue Support**: Background job processing for better performance
- **Customizable Configuration**: Extensive configuration options for all aspects
- **Migration Ready**: Automatic database setup and migrations

## 🚀 Installation

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

### Step 4: Configure Environment Variables

Add the following variables to your `.env` file:

```env
# WebSocket Server Configuration
SOCKEON_HOST=localhost
SOCKEON_PORT=8080
SOCKEON_KEY=your-secret-key
SOCKEON_DEBUG=true

# Notification Configuration (Optional)
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

The package configuration file is located at `config/filament-push-notifications.php`. Here's an overview of the main configuration sections:

### Socket Configuration

```php
'socket' => [
    'host' => env('SOCKEON_HOST', 'localhost'),
    'port' => env('SOCKEON_PORT', 8080),
    'key' => env('SOCKEON_KEY', 'secret'),
    'debug' => env('SOCKEON_DEBUG', true),
    'allowed_origins' => ['http://127.0.0.1:8000', 'http://localhost:8000'],
],
```

### Browser Notification Configuration

```php
'browser_notification' => [
    'favicon' => env('NOTIFICATION_FAVICON', 'https://example.com/favicon.ico'),
    'url' => env('NOTIFICATION_DEFAULT_URL', 'https://example.com'),
    'tag' => env('NOTIFICATION_TAG', 'default'),
    'require_interaction' => env('NOTIFICATION_REQUIRE_INTERACTION', false),
    'vibrate' => [100, 100, 100],
    'silent' => env('NOTIFICATION_SILENT', false),
    'badge' => env('NOTIFICATION_BADGE', 'https://example.com/badge.ico'),
    'dir' => env('NOTIFICATION_DIR', 'auto'),
    'lang' => env('NOTIFICATION_LANG', 'en'),
    'renotify' => env('NOTIFICATION_RENOTIFY', false),
    'timeout' => env('NOTIFICATION_TIMEOUT', 5000),
],
```

## 📱 Usage

### Starting the WebSocket Server

```bash
php artisan sockeon:start
```

## 🎯 Notification Types

### Browser Notifications

Browser notifications appear as native system notifications and support:
- Custom icons and badges
- Vibration patterns (mobile devices)
- Click actions and URL navigation
- Sound and interaction requirements
- Language and direction settings

### Filament Notifications

In-app notifications that appear within the Filament admin panel:
- Toast-style notifications
- Customizable appearance
- Auto-dismiss functionality
- Progress indicators
- Dark mode support

## 🗄️ Database Structure

The package creates a `push_notifications` table with the following structure:

```php
Schema::create('push_notifications', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('message');
    $table->enum('type', ['browser', 'filament']);
    $table->json('receivers');
    $table->timestamp('scheduled_at')->nullable();
    $table->timestamps();
});
```

## 🔌 Frontend Integration

The package automatically injects the notification system into your Filament admin panel. The JavaScript handles:

- WebSocket connections
- Real-time notification delivery
- Browser notification permissions
- Notification display and management
- Auto-dismiss and progress tracking

### Customization

You can customize the notification appearance by publishing and modifying the view:

```bash
php artisan vendor:publish --tag=filament-push-notifications-views
```

## 📚 API Reference

### Models

#### PushNotification

```php
class PushNotification extends Model
{
    protected $fillable = [
        'title',
        'message', 
        'type',
        'receivers',
        'scheduled_at',
    ];
    
    protected $casts = [
        'receivers' => 'array',
        'type' => PushNotificationType::class,
        'scheduled_at' => 'datetime',
    ];
}
```

### Enums

#### PushNotificationType

```php
enum PushNotificationType: string
{
    case BROWSER = 'browser';
    case FILAMENT = 'filament';
}
```

### Events

#### NotificationPushedEvent

```php
class NotificationPushedEvent
{
    public function __construct(
        public array $notification
    ) {}
}
```

## 🔧 Troubleshooting

### Common Issues

1. **WebSocket Connection Failed**
   - Ensure the Sockeon server is running
   - Check host and port configuration
   - Verify firewall settings

2. **Browser Notifications Not Working**
   - Check browser notification permissions
   - Ensure HTTPS is used (required for notifications)
   - Verify favicon and badge URLs are accessible

3. **Notifications Not Appearing**
   - Check WebSocket connection status
   - Verify user authentication
   - Check browser console for errors

### Debug Mode

Enable debug mode in your `.env` file:

```env
SOCKEON_DEBUG=true
```

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Development Setup

1. Fork the repository
2. Clone your fork
3. Install dependencies: `composer install`
5. Submit a pull request

## 📄 License

This package is open-sourced software licensed under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- [Filament](https://filamentphp.com/) for the amazing admin panel framework
- [Sockeon](https://github.com/sockeon/sockeon) for WebSocket server implementation
- [Laravel](https://laravel.com/) for the robust PHP framework

## 📞 Support

- **Documentation**: [GitHub Wiki](https://github.com/xentixar/filament-push-notifications/wiki)
- **Issues**: [GitHub Issues](https://github.com/xentixar/filament-push-notifications/issues)
- **Discussions**: [GitHub Discussions](https://github.com/xentixar/filament-push-notifications/discussions)
- **Email**: xentixar@gmail.com

---

**Made with ❤️ by [xentixar](https://github.com/xentixar)**