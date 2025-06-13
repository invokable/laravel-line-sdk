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

## Reference Documentation

## Glossary of Codebase-Specific Terms

**Bot** - Primary facade (`Revolution\Line\Facades\Bot`) providing static access to LINE Messaging API functionality including reply, push, and event parsing

**BotClient** - Core wrapper class (`src/Messaging/BotClient.php`) that encapsulates the LINE MessagingApiApi client and provides extensibility through macros

**BotFactory** - Contract (`src/Contracts/BotFactory.php`) defining interface for creating bot client instances with methods for bot(), botUsing(), reply(), and parseEvent()

**EventParser** - Trait (`src/Messaging/Concerns/EventParser.php`) providing event parsing functionality from LINE webhook requests

**LineChannel** - Laravel notification channel (`src/Notifications/LineChannel.php`) for dispatching notifications via LINE push messages

**LineMessage** - Fluent builder (`src/Notifications/LineMessage.php`) for constructing notification messages with text, stickers, images, videos, and custom sender/quick reply options

**LineLoginProvider** - Socialite provider (`src/Socialite/LineLoginProvider.php`) implementing LINE Login OAuth flow with scope and parameter customization

**LineSocialiteServiceProvider** - Service provider (`src/Providers/LineSocialiteServiceProvider.php`) extending Socialite with 'line-login' driver registration

**LineServiceProvider** - Primary service provider (`src/Providers/LineServiceProvider.php`) registering core LINE API client, bot factory, and webhook handler bindings

**MacroServiceProvider** - Service provider (`src/Providers/MacroServiceProvider.php`) adding `Http::line()` macro for pre-configured LINE API HTTP requests with authentication

**MessageListener** - Default event listener template (`stubs/listeners/Line/MessageListener.php`) handling incoming LINE message events with text and sticker responses

**ReplyMessage** - Fluent builder (`src/Messaging/ReplyMessage.php`) for constructing reply messages with chaining methods for text, stickers, images, and quick replies

**Replyable** - Trait (`src/Messaging/Concerns/Replyable.php`) providing `reply()` factory method for creating ReplyMessage instances

**RouteServiceProvider** - Service provider (`src/Providers/RouteServiceProvider.php`) registering `/line/webhook` route with ValidateSignature middleware and customizable path/domain

**ValidateSignature** - Middleware (`src/Messaging/Http/Middleware/ValidateSignature.php`) verifying LINE webhook request authenticity using channel secret

**WebhookController** - Single-action controller (`src/Messaging/Http/Controllers/WebhookController.php`) handling webhook requests by delegating to WebhookHandler implementation

**WebhookEventDispatcher** - Default webhook handler (`src/Messaging/Http/Actions/WebhookEventDispatcher.php`) dispatching parsed events to Laravel's event system

**WebhookHandler** - Contract (`src/Contracts/WebhookHandler.php`) defining interface for custom webhook processing logic

**WebhookLogHandler** - Alternative webhook handler (`src/Messaging/Http/Actions/WebhookLogHandler.php`) for logging webhook events

**WebhookNullHandler** - No-op webhook handler (`src/Messaging/Http/Actions/WebhookNullHandler.php`) for disabling webhook processing

**channel_token** - LINE Bot API access token from `config('line.bot.channel_token')` for authenticating Messaging API requests

**channel_secret** - LINE Bot channel secret from `config('line.bot.channel_secret')` used for webhook signature validation

**line-config** - Artisan publishing tag for `config/line.php` configuration file: `php artisan vendor:publish --tag=line-config`

**line-listeners** - Artisan publishing tag for event listener stubs to `app/Listeners/Line/`: `php artisan vendor:publish --tag=line-listeners`

**line-login** - Socialite driver identifier for LINE Login authentication integration

**reply token** - Temporary token from LINE MessageEvent required for sending reply messages, valid for limited time after webhook event

**routeNotificationForLine()** - Method that Laravel Notifiable models should implement to return the LINE user/group ID for notifications

**toLine()** - Method expected on Laravel Notification classes returning LineMessage instance for LINE notification channel

**withSender()** - Method on message builders for customizing sender display name and icon URL in LINE messages

**withQuickReply()** - Method on message builders for adding interactive quick reply buttons to LINE messages

**Http::line()** - Custom HTTP client macro providing pre-configured LINE API client with authentication headers and base URL

**MessagingApiApi** - Core LINE SDK client class from `linecorp/line-bot-sdk` package, wrapped by BotClient for enhanced functionality

**PushMessageRequest** - LINE SDK model for constructing push message payloads to specific user/group recipients

**ReplyMessageRequest** - LINE SDK model for constructing reply message payloads using reply tokens from webhook events

**TextMessageContent** - LINE SDK model representing text message content from webhook events

**StickerMessageContent** - LINE SDK model representing sticker message content from webhook events

**MessageEvent** - LINE webhook event model representing incoming messages from users

**FollowEvent** - LINE webhook event model representing users following the bot (handled by FollowListener stub)

**JoinEvent** - LINE webhook event model representing bot joining groups/rooms (handled by JoinListener stub)
