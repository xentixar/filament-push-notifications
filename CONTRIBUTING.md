# Contributing to Filament Push Notifications

Thank you for your interest in contributing to Filament Push Notifications! This document provides guidelines and information for contributors.

## 🤝 How to Contribute

We welcome contributions from the community! There are many ways you can help:

- 🐛 **Report bugs** - Help us identify and fix issues
- 💡 **Suggest features** - Share your ideas for improvements
- 📝 **Improve documentation** - Help make the docs clearer and more comprehensive
- 🔧 **Submit code** - Fix bugs, add features, or improve existing code
- 🧪 **Write tests** - Help ensure code quality and reliability
- 🌍 **Translate** - Help localize the package for different languages

## 📋 Before You Start

### Prerequisites

- **PHP 8.2+** - The package requires PHP 8.2 or higher
- **Laravel 11+** - Built for Laravel 11 and above
- **Filament 4.x** - Designed for Filament 4
- **Composer** - For dependency management
- **Git** - For version control

### Development Environment

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/filament-push-notifications.git
   cd filament-push-notifications
   ```
3. **Install dependencies**:
   ```bash
   composer install
   ```
4. **Set up testing environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

## 🔧 Development Workflow

### 1. Create a Feature Branch

Always work on a feature branch, never directly on `main`:

```bash
git checkout -b feature/your-feature-name
# or for bug fixes:
git checkout -b fix/issue-description
```

### 2. Make Your Changes

- **Follow coding standards** (see below)
- **Write tests** for new functionality
- **Update documentation** if needed
- **Keep commits atomic** and well-described

### 3. Commit Your Changes

Use conventional commit messages:

```bash
git commit -m "feat: add new notification type support"
git commit -m "fix: resolve WebSocket connection timeout issue"
git commit -m "docs: update installation instructions"
git commit -m "test: add tests for scheduled notifications"
```

### 4. Push and Create a Pull Request

```bash
git push origin feature/your-feature-name
```

Then create a Pull Request on GitHub with a clear description of your changes.

## 📝 Coding Standards

### PHP Standards

- **PSR-12** coding style
- **Type hints** for all method parameters and return types
- **DocBlocks** for all public methods and classes
- **Meaningful variable names** that describe their purpose
- **Single responsibility principle** - each class/method should do one thing well

### Example of Good Code

```php
<?php

declare(strict_types=1);

namespace Xentixar\FilamentPushNotifications\Services;

use Xentixar\FilamentPushNotifications\Models\PushNotification;
use Xentixar\FilamentPushNotifications\Enums\PushNotificationType;

/**
 * Service class for managing push notification operations.
 */
class NotificationService
{
    /**
     * Send a notification to specified users.
     *
     * @param string $title The notification title
     * @param string $message The notification message
     * @param PushNotificationType $type The notification type
     * @param array<int> $userIds Array of user IDs to notify
     * @param \DateTimeImmutable|null $scheduledAt When to send the notification
     * 
     * @return PushNotification The created notification instance
     * 
     * @throws \InvalidArgumentException When user IDs array is empty
     */
    public function sendNotification(
        string $title,
        string $message,
        PushNotificationType $type,
        array $userIds,
        ?\DateTimeImmutable $scheduledAt = null
    ): PushNotification {
        if (empty($userIds)) {
            throw new \InvalidArgumentException('User IDs array cannot be empty');
        }

        return PushNotification::create([
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'receivers' => $userIds,
            'scheduled_at' => $scheduledAt,
        ]);
    }
}
```

### JavaScript Standards

- **ES6+** syntax
- **Consistent indentation** (2 or 4 spaces)
- **Meaningful function names**
- **Error handling** for async operations
- **Comments** for complex logic

### Example of Good JavaScript

```javascript
/**
 * Creates and displays a browser notification.
 * 
 * @param {Object} notificationData - The notification data
 * @param {string} notificationData.title - Notification title
 * @param {string} notificationData.message - Notification message
 * @param {string} [notificationData.icon] - Custom icon URL
 * @returns {Notification|null} The created notification or null if failed
 */
function createBrowserNotification(notificationData) {
    try {
        // Check if browser supports notifications
        if (!('Notification' in window)) {
            console.warn('Browser notifications not supported');
            return null;
        }

        // Check permission status
        if (Notification.permission !== 'granted') {
            console.warn('Notification permission not granted');
            return null;
        }

        const notification = new Notification(notificationData.title, {
            body: notificationData.message,
            icon: notificationData.icon || defaultIcon,
            badge: defaultBadge,
            tag: 'default',
        });

        // Set up auto-close timer
        if (!notification.requireInteraction) {
            setTimeout(() => notification.close(), 5000);
        }

        return notification;
    } catch (error) {
        console.error('Failed to create notification:', error);
        return null;
    }
}
```

## 📚 Documentation Standards

### README Updates

- **Clear examples** with copy-paste code
- **Screenshots** for UI changes
- **Updated installation** steps if needed
- **Changelog** entries for new features

### Code Documentation

- **PHPDoc blocks** for all public methods
- **Inline comments** for complex logic
- **README examples** that actually work
- **API documentation** for public interfaces

## 🚀 Pull Request Guidelines

### Before Submitting

- [ ] **Code follows** coding standards
- [ ] **Documentation updated** if needed
- [ ] **No breaking changes** unless discussed
- [ ] **Commit messages** follow conventions

### PR Description Template

```markdown
## Description
Brief description of what this PR accomplishes.

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Screenshots (if applicable)
Add screenshots for UI changes.

## Checklist
- [ ] My code follows the style guidelines of this project
- [ ] I have performed a self-review of my own code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
```

## 🐛 Bug Reports

### Bug Report Template

```markdown
## Bug Description
Clear and concise description of the bug.

## Steps to Reproduce
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

## Expected Behavior
What you expected to happen.

## Actual Behavior
What actually happened.

## Environment
- **OS**: [e.g. Ubuntu 22.04]
- **PHP Version**: [e.g. 8.2.0]
- **Laravel Version**: [e.g. 11.0.0]
- **Package Version**: [e.g. 1.0.0]

## Additional Context
Any other context about the problem.
```

## 💡 Feature Requests

### Feature Request Template

```markdown
## Feature Description
Clear and concise description of the feature you'd like to see.

## Use Case
Describe the problem this feature would solve or the improvement it would provide.

## Proposed Solution
If you have ideas on how to implement this feature, share them here.

## Alternatives Considered
Any alternative solutions or features you've considered.

## Additional Context
Any other context, screenshots, or examples about the feature request.
```

## 🔒 Security

### Security Policy

- **Report security issues** privately to xentixar@gmail.com
- **Don't disclose** security issues publicly until fixed
- **Follow responsible disclosure** practices
- **Security fixes** get priority over other changes

## 📞 Getting Help

### Communication Channels

- **GitHub Issues**: For bugs and feature requests
- **GitHub Discussions**: For questions and general discussion
- **Email**: xentixar@gmail.com for private matters

### Before Asking for Help

1. **Check existing issues** - Your question might already be answered
2. **Search documentation** - The answer might be in the docs
3. **Provide context** - Include relevant code and error messages
4. **Be specific** - Describe exactly what you're trying to do

## 🎉 Recognition

Contributors will be recognized in:

- **README contributors section**
- **Release notes**
- **GitHub contributors list**
- **Special thanks** for significant contributions

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the same MIT License that covers the project.

---

**Thank you for contributing to Filament Push Notifications! 🚀**

Your contributions help make this package better for everyone in the Laravel and Filament community. 