# Messaging API / Bot

https://developers.line.biz/en/docs/messaging-api/

## Bot
`Revolution\Line\Facades\Bot` can use all methods of the `LINE\Clients\MessagingApi\Api\MessagingApiApi` class.

Delegate to LINEBot.
```php
use Revolution\Line\Facades\Bot;

Bot::replyMessage();
Bot::pushMessage();
```

It also has the original `reply` function.

```php
use Revolution\Line\Facades\Bot;

Bot::reply($token)->text('text');
Bot::reply($token)->withSender('alt-name')->text('text1', 'text2');
Bot::reply($token)->sticker(package: 1, sticker: 1);
```

## Webhook

The SDK includes Webhook routing and controller.

### Webhook URL
`https://localhost/line/webhook`

You can change `line/webhook` in .env

```
LINE_BOT_WEBHOOK_PATH=webhook
```

### Working with Laravel Event System
When a Webhook event is received, Laravel event is dispatching.

Note: In production, you should run `php artisan event:cache` command.

### Publishing default Listeners
Publish to `app/Listeners/Line/`.
```
php artisan vendor:publish --tag=line-listeners
```

`LINE\Webhook\Model\MessageEvent` event is handled by `MessageListener`.

```php
namespace App\Listeners\Line;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use LINE\Clients\MessagingApi\ApiException;
use LINE\Webhook\Model\MessageEvent;
use LINE\Webhook\Model\StickerMessageContent;
use LINE\Webhook\Model\TextMessageContent;
use Revolution\Line\Facades\Bot;

class MessageListener
{
    protected string $token;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MessageEvent  $event
     * @return void
     * @throws ApiException
     */
    public function handle(MessageEvent $event): void
    {
        $message = $event->getMessage();
        $this->token = $event->getReplyToken();

        match ($message::class) {
            TextMessageContent::class => $this->text($message),
            StickerMessageContent::class => $this->sticker($message),
        };
    }

    /**
     * @throws ApiException
     */
    protected function text(TextMessageContent $message): void
    {
        Bot::reply($this->token)->text($message->getText());
    }

    /**
     * @throws ApiException
     */
    protected function sticker(StickerMessageContent $message): void
    {
        Bot::reply($this->token)->sticker(
            $message->getPackageId(),
            $message->getStickerId()
        );
    }
}
```

## Customizing

### Bot macro
`Bot` is Macroable, it means "You can add any method"

Register at `AppServiceProvider@boot`
```php
use Revolution\Line\Facades\Bot;

    public function boot()
    {
        Bot::macro('foo', function () {
            return $this->bot()->...
        });
    }
```
Use it anywhere.
```php
$foo = Bot::foo();
```

### Replacing `MessagingApiApi` instance
`Bot::bot()` returns MessagingApiApi instance. You can swap instances with `Bot::botUsing()`

```php
$bot = new MyBot();

Bot::botUsing($bot);
```
Accepts a callable.
```php
Bot::botUsing(function () {
   return new MyBot();
});
```

### Webhook default route middleware
The `throttle` middleware is enabled. To disable it, configure in `.env`.

```
LINE_BOT_WEBHOOK_MIDDLEWARE=null
```

Or change the `throttle` settings.

```
LINE_BOT_WEBHOOK_MIDDLEWARE=throttle:120,1
```

Laravel>=8
```php
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('line', function (Request $request) {
    return Limit::perMinute(120);
});
```
```
LINE_BOT_WEBHOOK_MIDDLEWARE=throttle:line
```

### Another way not to use the Laravel Event system

Make your `app/Actions/LineWebhook.php`

```php
<?php
namespace App\Actions;

use Illuminate\Http\Request;
use Revolution\Line\Contracts\WebhookHandler;
use Revolution\Line\Facades\Bot;
use LINE\Webhook\Model\MessageEvent;

class LineWebhook implements WebhookHandler
{
    /**
     * @param  Request  $request
     * @return mixed
     */
    public function __invoke(Request $request): mixed
    {
        Bot::parseEvent($request)->each(function ($event) {
            //event($event);
            if($event instanceof MessageEvent){
                //
            }
        });

        return response('OK');
    }
}
```

Register at `AppServiceProvider@register`
```php
use App\Actions\LineWebhook;
use Revolution\Line\Contracts\WebhookHandler;

public function register()
{
    $this->app->singleton(WebhookHandler::class, LineWebhook::class);
}
```

Anything is possible by replacing the WebhookHandler.

### Http::line() (Required Laravel>=7)
We've already extended the `Http` class, so you can make API requests without using the LINEBot class.

```php
use Illuminate\Support\Facades\Http;

$response = Http::line()->post('/v2/bot/channel/webhook/test', [
                            'endpoint' => '',
                        ]);
```

Combine with Bot macro.

```php
use Illuminate\Support\Facades\Http;
use Revolution\Line\Facades\Bot;

Bot::macro('verifyWebhook', function ($endpoint = '') {
    return Http::line()->post('/v2/bot/channel/webhook/test', [
        'endpoint' => $endpoint,
    ])->json();
});
```

```php
$response = Bot::verifyWebhook();
```
