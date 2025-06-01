<?php

namespace Revolution\Line\Providers;

use Illuminate\Support\ServiceProvider;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;
use Revolution\Line\Contracts\BotFactory;
use Revolution\Line\Contracts\WebhookHandler;
use Revolution\Line\Messaging\BotClient;
use Revolution\Line\Messaging\Http\Actions\WebhookEventDispatcher;

class LineServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/line.php',
            'line'
        );

        $this->registerBot();

        $this->registerWebhookHandler();
    }

    /**
     * Bot.
     */
    protected function registerBot(): void
    {
        $this->app->scoped(MessagingApiApi::class, function ($app) {
            $config = (new Configuration)->setAccessToken(config('line.bot.channel_token'));

            return new MessagingApiApi(config: $config);
        });

        $this->app->scoped(BotFactory::class, BotClient::class);
    }

    /**
     * Default WebhookHandler.
     */
    protected function registerWebhookHandler(): void
    {
        $this->app->scoped(WebhookHandler::class, WebhookEventDispatcher::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configurePublishing();
    }

    /**
     * Configure publishing for the package.
     */
    protected function configurePublishing(): void
    {
        if (! $this->app->runningInConsole()) {
            return; // @codeCoverageIgnore
        }

        $this->publishes([
            __DIR__.'/../../config/line.php' => $this->app->configPath('line.php'),
        ], 'line-config');

        $this->publishes([
            __DIR__.'/../../stubs/listeners' => $this->app->path('Listeners'),
        ], 'line-listeners');
    }
}
