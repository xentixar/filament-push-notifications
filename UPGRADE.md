# Upgrading from 1.x to 2.x

This guide will help you upgrade from version 1.x to 2.x of the Filament Push Notifications package.

## Overview of Changes

Version 2.x introduces **Web Push API** support with service workers, enabling persistent native notifications that work even when the browser is closed. This is a major enhancement over the 1.x version which relied solely on the browser's native Notification API.

### Major Features Added in 2.x

- ✨ **Web Push API Integration**: Service worker-based persistent notifications
- 🔐 **VAPID Authentication**: Secure push notification delivery
- 📱 **Push Subscription Management**: User subscription handling with UI toggle
- 🔧 **Enhanced WebSocket Configuration**: Separate internal/external host configuration for WSS support
- 🚀 **Production Ready**: Full SSL/WSS support with reverse proxy configuration
- 📦 **New Dependencies**: Added `minishlink/web-push` library and `ext-bcmath`

## Breaking Changes

### 1. Composer Dependencies

**New required dependencies:**
- `minishlink/web-push: ^9.0`
- `ext-bcmath` (required)

**Suggested extensions for better performance:**
- `ext-gmp` (alternative to bcmath)

### 2. Database Changes

A new table `push_subscriptions` is required to store user push notification subscriptions.

### 3. Configuration Changes

The configuration file has significant additions:

**New Web Push configuration section:**
```php
'web_push' => [
    'vapid_public_key' => env('VAPID_PUBLIC_KEY'),
    'vapid_private_key' => env('VAPID_PRIVATE_KEY'),
    'vapid_subject' => env('VAPID_SUBJECT', 'mailto:admin@example.com'),
    'ttl' => env('WEB_PUSH_TTL', 2419200),
    'urgency' => env('WEB_PUSH_URGENCY', 'normal'),
    'topic' => env('WEB_PUSH_TOPIC', null),
],
```

**Enhanced WebSocket configuration:**
```php
'socket' => [
    // ... existing configs
    'external_host' => env('SOCKEON_EXTERNAL_HOST', env('SOCKEON_HOST', 'localhost')),
    'external_port' => env('SOCKEON_EXTERNAL_PORT', env('SOCKEON_PORT', 8080)),
],
```

### 4. Command Changes

The Sockeon start command has been renamed:
- **Old**: `php artisan sockeon:start`
- **New**: `php artisan start:sockeon`

### 5. New Assets

The package now includes a service worker file that needs to be published to the public directory.

## Step-by-Step Upgrade Guide

### Step 1: Update Composer Dependencies

Update your `composer.json` to require the new dependencies:

```bash
composer update xentixar/filament-push-notifications
```

This will automatically pull in the new dependencies (`minishlink/web-push` and require `ext-bcmath`).

### Step 2: Install PHP Extensions

Ensure you have the required PHP extension installed:

```bash
# For Debian/Ubuntu
sudo apt-get install php-bcmath

# Or for better performance, install GMP
sudo apt-get install php-gmp
```

After installation, restart your PHP service (PHP-FPM, Apache, etc.).

### Step 3: Run Migrations

The package includes a new migration for the `push_subscriptions` table:

```bash
php artisan vendor:publish --tag=filament-push-notifications-migrations
php artisan migrate
```

This will create the `push_subscriptions` table with the following structure:
- `id`
- `user_id` (polymorphic relation to receiver model)
- `user_type`
- `endpoint`
- `public_key`
- `auth_token`
- `content_encoding`
- `timestamps`

### Step 4: Publish Configuration (if customized)

If you have previously customized the configuration file, you'll need to merge the new options. Compare your existing config with the new one:

```bash
# Backup your current config
cp config/filament-push-notifications.php config/filament-push-notifications.php.backup

# Publish the new config
php artisan vendor:publish --tag=filament-push-notifications-config --force

# Manually merge your customizations from the backup
```

**Important**: Don't skip this step if you've customized notification settings, as the structure has changed slightly.

### Step 5: Generate VAPID Keys

VAPID (Voluntary Application Server Identification) keys are required for Web Push API authentication:

```bash
php artisan push:generate-vapid-keys --update-env
```

This command will:
- Generate a new VAPID key pair (public and private)
- Automatically add them to your `.env` file
- Display the keys in case you need to manually configure them

**Important**: Keep your VAPID private key secret! Never expose it in client-side code or version control.

### Step 6: Publish Service Worker Assets

Publish the service worker JavaScript file to your public directory:

```bash
php artisan vendor:publish --tag=filament-push-notifications-assets
```

This will publish:
- `public/service-worker.js` - The service worker for handling push notifications

**Note**: If you've previously published views or other assets, this won't overwrite them.

### Step 7: Update Environment Variables

Add the following new environment variables to your `.env` file (they may have been added automatically in Step 5):

```env
# Web Push VAPID Keys (Generated via: php artisan push:generate-vapid-keys)
VAPID_PUBLIC_KEY="your-generated-public-key"
VAPID_PRIVATE_KEY="your-generated-private-key"
VAPID_SUBJECT="mailto:admin@yourdomain.com"

# Web Push Configuration (Optional - defaults shown)
WEB_PUSH_TTL=2419200          # 4 weeks in seconds
WEB_PUSH_URGENCY=normal       # Options: very-low, low, normal, high
WEB_PUSH_TOPIC=               # Optional: for replacing notifications

# For Production with SSL/WSS Support
SOCKEON_HOST=127.0.0.1                    # Internal bind address
SOCKEON_PORT=8080                         # Internal bind port
SOCKEON_EXTERNAL_HOST=ws.yourdomain.com   # Public WebSocket host
SOCKEON_EXTERNAL_PORT=443                 # Public WebSocket port (SSL)
```

**Important**: Update `VAPID_SUBJECT` with your actual contact email or domain URL.

### Step 8: Update Sockeon Start Command

If you have any scripts or process managers (like Supervisor) that start the Sockeon server, update the command:

**Old command:**
```bash
php artisan sockeon:start
```

**New command:**
```bash
php artisan start:sockeon
```

**For Supervisor:**
```ini
[program:sockeon]
command=php /path/to/your/project/artisan start:sockeon
```

### Step 9: Clear Caches

Clear all Laravel caches to ensure the new configurations are loaded:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## New Features to Utilize

### 1. Web Push Subscriptions

Users can now subscribe to web push notifications through the Filament admin panel. A subscription toggle button will automatically appear in the bottom-right corner of the panel.

**User Flow:**
1. User clicks "Enable Web Push" button
2. Browser prompts for notification permission
3. If granted, subscription is stored in the `push_subscriptions` table
4. Native notifications will now be delivered via Web Push API

### 2. Service Worker Benefits

The service worker enables:
- **Persistent notifications**: Work even when browser is closed
- **Offline capability**: Notifications queued and delivered when back online
- **Background sync**: Reliable delivery mechanism
- **Click actions**: Handle notification clicks even when app isn't running

### 3. Production SSL/WSS Support

Version 2.x fully supports secure WebSocket connections (WSS) required for HTTPS sites:

**Recommended Setup:**
1. Create a subdomain for WebSocket (e.g., `ws.yourdomain.com`)
2. Configure Nginx as a reverse proxy for SSL termination
3. Use separate `SOCKEON_EXTERNAL_*` environment variables

See the README for detailed Nginx configuration examples.

## Testing the Upgrade

After completing the upgrade, test the following:

### 1. Web Push Subscription
- [ ] Open the Filament admin panel
- [ ] Look for the "Enable Web Push" button (bottom-right)
- [ ] Click to subscribe and grant browser permission
- [ ] Verify subscription is saved in `push_subscriptions` table

### 2. Send Test Notification
- [ ] Create a new push notification in the admin panel
- [ ] Set type to "Native"
- [ ] Send to your user
- [ ] Verify notification is received

### 3. WebSocket Connection
- [ ] Open browser developer console
- [ ] Check for WebSocket connection messages
- [ ] Verify no connection errors
- [ ] Test with HTTPS if in production

### 4. Service Worker Registration
- [ ] Open browser DevTools → Application → Service Workers
- [ ] Verify `service-worker.js` is registered and active
- [ ] Check for any service worker errors

## Troubleshooting

### Issue: VAPID keys not generated

**Solution:**
```bash
php artisan push:generate-vapid-keys --update-env
```

### Issue: Service worker not found (404)

**Cause**: Service worker not published or cleared

**Solution:**
```bash
php artisan vendor:publish --tag=filament-push-notifications-assets --force
```

### Issue: Web Push button not appearing

**Possible causes:**
1. Service worker not registered
2. Not using HTTPS (required in production)
3. Browser doesn't support Push API
4. JavaScript errors - check browser console

### Issue: WebSocket connection fails on HTTPS

**Cause**: Mixed content (HTTP WebSocket on HTTPS site)

**Solution**: Configure WSS using reverse proxy as documented in README

### Issue: Push notifications not delivered

**Check:**
1. VAPID keys are correctly set
2. User has an active subscription in `push_subscriptions` table
3. Browser permission is granted
4. Service worker is active
5. WebSocket connection is established

### Issue: "ext-bcmath" not found

**Solution:**
```bash
# Debian/Ubuntu
sudo apt-get install php-bcmath
sudo systemctl restart php8.2-fpm  # Adjust version as needed

# Check installation
php -m | grep bcmath
```

## Rolling Back

If you need to rollback to version 1.x:

```bash
# 1. Update composer.json
composer require xentixar/filament-push-notifications:^1.0

# 2. Rollback the migration (CAUTION: This deletes subscription data)
php artisan migrate:rollback --step=1

# 3. Restore your old config file
cp config/filament-push-notifications.php.backup config/filament-push-notifications.php

# 4. Remove service worker
rm public/service-worker.js

# 5. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# 6. Restart Sockeon with old command
php artisan sockeon:start
```

## Getting Help

If you encounter issues during the upgrade:

1. Check the [GitHub Issues](https://github.com/xentixar/filament-push-notifications/issues)
2. Review the [README.md](README.md) for detailed configuration
3. Check browser console for JavaScript errors
4. Verify all environment variables are set correctly
5. Ensure all PHP extensions are installed

## Summary of File Changes

**New Files:**
- `src/Console/Commands/GenerateVapidKeysCommand.php`
- `src/Controllers/PushSubscriptionController.php`
- `src/Models/PushSubscription.php`
- `src/Services/WebPushService.php`
- `database/migrations/create_push_subscriptions_table.php.stub`
- `resources/js/service-worker.js`
- `routes/web.php` (new routes added)

**Modified Files:**
- `composer.json` (new dependencies)
- `config/filament-push-notifications.php` (web push config added)
- `src/PushNotificationsServiceProvider.php` (service worker registration)
- `src/Jobs/SchedulePushNotificationJob.php` (web push integration)
- `src/Console/Commands/StartSockeonCommand.php` (logging improvements)
- `resources/views/notification.blade.php` (web push UI)
- `README.md` (updated documentation)

**Removed Files:**
- `CONTRIBUTING.md` (moved to main repository)

## Important Notes

1. **HTTPS Required**: Web Push API requires HTTPS in production (not in localhost development)
2. **Browser Support**: Web Push works on Chrome, Firefox, Edge, Opera, and Safari 16+
3. **User Consent**: Users must explicitly grant permission for push notifications
4. **Backward Compatible**: Local (in-app) notifications continue to work as before
5. **Performance**: Consider using `ext-gmp` for better cryptographic performance

## What's Next?

After upgrading, explore the new features:

- Customize service worker behavior
- Implement notification click actions
- Set up production WSS with Nginx
- Monitor subscription metrics
- Customize notification appearance

Congratulations! You've successfully upgraded to version 2.x 🎉
