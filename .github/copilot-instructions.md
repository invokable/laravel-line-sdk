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
```
LINE_BOT_CHANNEL_TOKEN=your_channel_token
LINE_BOT_CHANNEL_SECRET=your_channel_secret  
LINE_LOGIN_CLIENT_ID=your_client_id
LINE_LOGIN_CLIENT_SECRET=your_client_secret
LINE_LOGIN_REDIRECT=https://yourapp.com/callback
```

**Main Configuration** (`config/line.php`):
- Bot API credentials and webhook settings
- LINE Login OAuth configuration
- Route and middleware customization

### Core Classes and Functions

**Primary API Interface**:
```php
// Facade for LINE Messaging API
Bot::reply($token)->text('Hello World');
Bot::pushMessage($pushRequest);
Bot::parseEvent($request);

// Notification Channel
$user->notify(new LineNotification());

// Socialite Integration  
Socialite::driver('line-login')->redirect();
```

**Key Components**:
- `Revolution\Line\Messaging\BotClient` - Main API client wrapper
- `Revolution\Line\Messaging\ReplyMessage` - Message builder for replies
- `Revolution\Line\Notifications\LineMessage` - Message builder for notifications
- `Revolution\Line\Messaging\Http\Actions\WebhookEventDispatcher` - Webhook event processor

### Testing and Development

**Testing Infrastructure**:
- PHPUnit configuration with multiple PHP version matrix (8.2, 8.3, 8.4)
- GitHub Actions for automated testing and linting
- Code coverage reporting via Clover XML
- Laravel Pint for code style enforcement

**Development Workflow**:
1. Tests run automatically on push/PR via GitHub Actions
2. Code style enforced via Laravel Pint
3. Multiple PHP/Laravel version compatibility testing
4. Webhook signature validation for security

## Glossary of Codebase-Specific Terms

**Bot** - Primary facade (`Revolution\Line\Facades\Bot`) providing static access to LINE Messaging API functionality

**BotClient** - Core wrapper class (`src/Messaging/BotClient.php`) that encapsulates MessagingApiApi and provides extensibility

**BotFactory** - Contract (`src/Contracts/BotFactory.php`) defining interface for creating bot client instances

**LineChannel** - Laravel notification channel (`src/Notifications/LineChannel.php`) for dispatching notifications via LINE

**LineMessage** - Fluent builder (`src/Notifications/LineMessage.php`) for constructing notification messages with text, stickers, images

**LineLoginProvider** - Socialite provider (`src/Socialite/LineLoginProvider.php`) implementing LINE Login OAuth flow

**LineSocialiteServiceProvider** - Service provider extending Socialite with 'line-login' driver registration

**LineServiceProvider** - Primary service provider registering core LINE API client and webhook handler bindings

**MacroServiceProvider** - Service provider adding `Http::line()` macro for pre-configured LINE API HTTP requests

**MessageListener** - Default event listener handling incoming LINE message events (published to `app/Listeners/Line/`)

**ReplyMessage** - Fluent builder (`src/Messaging/ReplyMessage.php`) for constructing reply messages with chaining methods

**Replyable** - Trait (`src/Messaging/Concerns/Replyable.php`) providing `reply()` factory method for other classes

**RouteServiceProvider** - Service provider registering `/line/webhook` route with ValidateSignature middleware

**ValidateSignature** - Middleware (`src/Messaging/Http/Middleware/ValidateSignature.php`) verifying LINE webhook request authenticity

**WebhookController** - Single-action controller (`src/Messaging/Http/Controllers/WebhookController.php`) handling webhook requests

**WebhookEventDispatcher** - Default webhook handler dispatching parsed events to Laravel's event system

**WebhookHandler** - Contract (`src/Contracts/WebhookHandler.php`) defining interface for custom webhook processing logic

**channel_token** - LINE Bot API access token from config('line.bot.channel_token') for API authentication

**line-config** - Artisan publishing tag for `config/line.php` configuration file

**line-listeners** - Artisan publishing tag for event listener stubs to `app/Listeners/Line/`

**line-login** - Socialite driver identifier for LINE Login authentication integration

**reply token** - Temporary token from LINE MessageEvent required for sending reply messages within time limit

**toLine()** - Method expected on Laravel Notification classes returning LineMessage for LINE channel

**withSender()** - Method on message builders for customizing sender name and icon display

**withQuickReply()** - Method on message builders for adding interactive quick reply buttons

**Http::line()** - Custom HTTP client macro providing pre-configured LINE API client with authentication

**MessagingApiApi** - Core LINE SDK client class wrapped by BotClient for direct API interactions

**PushMessageRequest** - LINE SDK model for constructing push message payloads to specific recipients
