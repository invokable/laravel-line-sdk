# Laravel LINE SDK Onboarding Guide

## Overview

The `revolution/laravel-line-sdk` is a Laravel package that provides comprehensive integration with LINE platform services. It enables Laravel developers to build applications that communicate with LINE users through two primary channels:

**LINE Messaging API Integration**: Allows applications to function as LINE bots that can send and receive messages, handle rich content (images, videos, stickers), and process webhook events from the LINE platform. This enables businesses to create customer service bots, notification systems, and interactive messaging experiences.

**LINE Login Integration**: Provides OAuth authentication allowing users to log into Laravel applications using their LINE accounts, similar to "Login with Google" or "Login with Facebook" functionality.

**Target Users**: Laravel developers building:
- Customer service chatbots
- Notification and alert systems
- E-commerce applications with LINE integration
- Social applications requiring LINE authentication
- Marketing automation platforms

The package abstracts the complexity of LINE's APIs behind familiar Laravel patterns (Facades, Notifications, Events, Socialite) while maintaining full access to underlying LINE SDK functionality.

## Project Organization

### Core Architecture

The project follows Laravel package conventions with several key architectural layers:

**Service Provider Layer** (`src/Providers/`):
- `LineServiceProvider.php` - Core provider registering LINE API client and webhook handlers
- `RouteServiceProvider.php` - Registers webhook endpoints and middleware
- `MacroServiceProvider.php` - Extends Laravel's HTTP client with LINE-specific functionality
- `LineSocialiteServiceProvider.php` - Integrates LINE Login with Laravel Socialite

**API Integration Layer** (`src/Messaging/`, `src/Facades/`):
- `Bot.php` - Primary facade for LINE Messaging API interactions
- `BotClient.php` - Core wrapper around LINE's MessagingApiApi client
- `ReplyMessage.php` - Fluent builder for constructing reply messages
- HTTP controllers and middleware for webhook processing

**Notification System** (`src/Notifications/`):
- `LineChannel.php` - Laravel notification channel for LINE messaging
- `LineMessage.php` - Fluent builder for notification messages

**Authentication System** (`src/Socialite/`):
- `LineLoginProvider.php` - Socialite provider for LINE Login OAuth flow

## Package Configuration and Setup

### Package Installation and Setup

**Installation**:
```bash
composer require revolution/laravel-line-sdk
```

**Publishing Configuration**:
```bash
# Publish configuration file
php artisan vendor:publish --tag=line-config

# Publish event listener stubs
php artisan vendor:publish --tag=line-listeners
```

**Required Setup**:
1. Create LINE Developer Console channels (Messaging API + LINE Login)
2. Configure environment variables in `.env`
3. Set webhook URL in LINE Developer Console: `https://yourapp.com/line/webhook`
4. Set LINE Login callback URL: `https://yourapp.com/auth/line/callback`
5. Publish and customize event listeners for webhook handling

### Directory Structure

```
├── src/                          # Main source code
│   ├── Contracts/               # Interfaces (BotFactory, WebhookHandler)
│   ├── Facades/                 # Laravel facades (Bot)
│   ├── Messaging/               # Core messaging functionality
│   │   ├── Http/               # Webhook controllers and middleware
│   │   ├── Concerns/           # Reusable traits (Replyable)
│   │   └── BotClient.php       # Main LINE API client wrapper
│   ├── Notifications/          # Laravel notification integration
│   ├── Providers/              # Laravel service providers
│   └── Socialite/              # LINE Login integration
├── config/line.php             # Package configuration
├── tests/                      # PHPUnit test suite
├── stubs/listeners/            # Publishable event listener templates
├── docs/                       # Documentation and examples
└── .github/workflows/          # CI/CD pipelines
```

### Key Configuration Files

**Environment Configuration**:
```env
# LINE Messaging API (Bot Channel)
LINE_BOT_CHANNEL_TOKEN=your_channel_access_token
LINE_BOT_CHANNEL_SECRET=your_channel_secret

# LINE Login Channel
LINE_LOGIN_CLIENT_ID=your_client_id
LINE_LOGIN_CLIENT_SECRET=your_client_secret
LINE_LOGIN_REDIRECT=https://yourapp.com/auth/line/callback

# Optional webhook customization
LINE_BOT_WEBHOOK_PATH=line/webhook
LINE_BOT_WEBHOOK_ROUTE=line.webhook
LINE_BOT_WEBHOOK_DOMAIN=your-domain.com
LINE_BOT_WEBHOOK_MIDDLEWARE=throttle
```

**Main Configuration** (`config/line.php`):
- **Bot settings**: Channel token, secret, webhook path, route name, domain, and middleware
- **Login settings**: Client ID, secret, and OAuth redirect URL
- **Webhook routing**: Customizable path (`/line/webhook` by default) with signature validation
- **Middleware configuration**: Default throttling, customizable per environment

## Usage Examples and API Reference

### Core Classes and Functions

**Primary API Interface**:
```php
// Bot Facade - LINE Messaging API
Bot::reply($replyToken)->text('Hello World');
Bot::reply($replyToken)->sticker(446, 1988);
Bot::pushMessage($pushRequest);
Bot::parseEvent($request);

// Notification Channel
$user->notify(new LineNotification());
Notification::route('line', $userId)->notify(new LineNotification());

// Socialite Integration
Socialite::driver('line-login')->redirect();
$user = Socialite::driver('line-login')->user();
```

**Fluent Message Builders**:
```php
// Reply messages (requires reply token from webhook events)
Bot::reply($replyToken)
    ->text('Hello!')
    ->sticker(446, 1988)
    ->image('https://example.com/image.jpg', 'https://example.com/preview.jpg');

// Notification messages (push to any user)
LineMessage::create()
    ->text('Notification message')
    ->withSender('Bot Name', 'https://example.com/icon.png')
    ->withQuickReply($quickReplyObject);
```

**Event Handling**:
```php
// Default webhook event dispatcher
class MessageListener
{
    public function handle(MessageEvent $event): void
    {
        $message = $event->getMessage();
        $replyToken = $event->getReplyToken();
        
        if ($message instanceof TextMessageContent) {
            Bot::reply($replyToken)->text('Echo: ' . $message->getText());
        }
    }
}
```

**Key Components**:
- `Revolution\Line\Messaging\BotClient` - Main API client wrapper extending MessagingApiApi
- `Revolution\Line\Messaging\ReplyMessage` - Fluent builder for webhook reply messages
- `Revolution\Line\Notifications\LineMessage` - Fluent builder for push notification messages
- `Revolution\Line\Messaging\Http\Actions\WebhookEventDispatcher` - Default webhook event processor
- `Revolution\Line\Socialite\LineLoginProvider` - Socialite provider for LINE Login OAuth

## Development and Testing

### Testing and Development

**Testing Infrastructure**:
- **PHPUnit**: Comprehensive test suite with 33+ tests covering facades, notifications, and integrations
- **PHP Version Matrix**: Tests run on PHP 8.2, 8.3, and 8.4 for broad compatibility
- **Code Coverage**: Clover XML format reporting with Xdebug integration
- **Laravel Pint**: Automated code style enforcement following Laravel conventions
- **Orchestra Testbench**: Laravel package testing framework for isolated testing

**CI/CD Pipeline** (`.github/workflows/`):
- **`tests.yml`**: Automated testing across PHP versions on push/PR
- **`lint.yml`**: Code style validation with Laravel Pint
- **`copilot-setup-steps.yml`**: AI-assisted development workflow setup

**Development Workflow**:
1. **Automated Testing**: All tests run automatically on push/PR via GitHub Actions
2. **Code Quality**: Laravel Pint enforces consistent code style across the codebase
3. **Multi-Version Support**: Ensures compatibility across PHP 8.2+ and Laravel 11+
4. **Security**: Webhook signature validation prevents unauthorized requests
5. **Coverage Reporting**: Integration with Qlty for maintainability and coverage metrics

**Local Development**:
```bash
# Install dependencies
composer install

# Run tests
vendor/bin/phpunit

# Check code style
vendor/bin/pint --test

# Fix code style
vendor/bin/pint
```
