# LINE SDK for Laravel

[![packagist](https://badgen.net/packagist/v/revolution/laravel-line-sdk)](https://packagist.org/packages/revolution/laravel-line-sdk)
[![tests](https://github.com/invokable/laravel-line-sdk/actions/workflows/tests.yml/badge.svg)](https://github.com/invokable/laravel-line-sdk/actions/workflows/tests.yml)
[![Maintainability](https://qlty.sh/badges/937e8320-9fb3-4cda-bc1b-bd6325325f25/maintainability.svg)](https://qlty.sh/gh/invokable/projects/laravel-line-sdk)
[![Code Coverage](https://qlty.sh/badges/937e8320-9fb3-4cda-bc1b-bd6325325f25/test_coverage.svg)](https://qlty.sh/gh/invokable/projects/laravel-line-sdk)

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/invokable/laravel-line-sdk)

## Features
- Working with Laravel Event System. Including Webhook routing and controller.
- Extensible Bot Client.
- Working with Laravel Notification System(LINE Messaging API)
- Including Socialite drivers(LINE Login)

## Requirements
- PHP >= 8.2
- Laravel >= 11.0

## Installation

```
composer require revolution/laravel-line-sdk
```

### Uninstall
```shell
composer remove revolution/laravel-line-sdk
```

- Delete related files. See below.

## Configuration

### .env
Set up in LINE Developers console.
https://developers.line.biz/

> **Note**: You can no longer create a Messaging API channel directly from the LINE Developers Console.
To create a Messaging API channel, first create a LINE Official Account using the [Create LINE Official Account] button. Then, enable Messaging API usage from the LINE Official Account Manager.

Create two channels `Messaging API` and `LINE Login`.

- Messaging API : Get `Channel access token (long-lived)` and `Channel secret`. Set `Webhook URL`
- LINE Login : Get `Channel ID` and `Channel secret`. Set `Callback URL`

```
LINE_BOT_CHANNEL_TOKEN=
LINE_BOT_CHANNEL_SECRET=

LINE_LOGIN_CLIENT_ID=
LINE_LOGIN_CLIENT_SECRET=
LINE_LOGIN_REDIRECT=
```

### Publishing(Optional)

```
php artisan vendor:publish --tag=line-config
```

## Quick Start

### Prepare
- Create `Messaging API` channel in LINE Developers console.
- Get `Channel access token (long-lived)`, `Channel secret` and QR code.
- A web server that can receive webhooks from LINE. Not possible on a normal local server.

### Create new Laravel project
```
laravel new line-bot
cd ./line-bot
composer require revolution/laravel-line-sdk
```

Edit `.env`

```
LINE_BOT_CHANNEL_TOKEN=
LINE_BOT_CHANNEL_SECRET=
```

Publishing Listeners
```
php artisan vendor:publish --tag=line-listeners
```

### Deploy to web server
- Set `Webhook URL` in LINE Developers console. `https://example.com/line/webhook`
- Verify Webhook URL.

### Add bot as a friend.
- Using QR code.

### Send test message
Bot returns same message.

## Documents
- [Messaging API / Bot](./docs/bot.md)
- [Socialite](./docs/socialite.md)
- [Notifications](./docs/notification.md)

## Demo
https://github.com/invokable/laravel-line-project

## LICENSE
MIT
