<?php

namespace Tests\Messaging;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use Revolution\Line\Contracts\BotFactory;
use Revolution\Line\Facades\Bot;
use Revolution\Line\Messaging\Bot as BotAlias;
use Revolution\Line\Messaging\BotClient;
use Tests\TestCase;

class BotClientTest extends TestCase
{
    public function test_bot_instance()
    {
        $this->assertInstanceOf(MessagingApiApi::class, app(MessagingApiApi::class));
        $this->assertInstanceOf(BotClient::class, app(BotFactory::class));
    }

    public function test_bot_using()
    {
        $bot = app(MessagingApiApi::class);
        $client = new BotClient($bot);

        $client->botUsing($bot);
        $this->assertInstanceOf(MessagingApiApi::class, $client->bot());

        $client->botUsing(function () use ($bot) {
            return $bot;
        });
        $this->assertInstanceOf(MessagingApiApi::class, $client->bot());
    }

    public function test_macroable()
    {
        Bot::macro('testMacro', function () {
            return 'test';
        });

        $this->assertSame('test', Bot::testMacro());
    }

    public function test_bot_info()
    {
        $this->mock(MessagingApiApi::class, function ($mock) {
            $mock->shouldReceive('getBotInfo')
                ->once()
                ->andReturn([]);
        });

        $this->assertSame([], Bot::getBotInfo());
    }

    public function test_bot_alias()
    {
        $this->assertInstanceOf(MessagingApiApi::class, BotAlias::bot());
    }

    /**
     * @requires function \Illuminate\Http\Client\PendingRequest::get
     */
    public function test_http_macro()
    {
        $this->assertInstanceOf(PendingRequest::class, Http::line());
    }
}
