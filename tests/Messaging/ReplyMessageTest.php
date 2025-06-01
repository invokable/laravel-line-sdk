<?php

namespace Tests\Messaging;

use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\QuickReply;
use LINE\Clients\MessagingApi\Model\ReplyMessageResponse;
use LINE\Clients\MessagingApi\Model\TextMessage;
use LINE\Constants\MessageType;
use Mockery as m;
use Revolution\Line\Facades\Bot;
use Revolution\Line\Messaging\ReplyMessage;
use Tests\TestCase;

class ReplyMessageTest extends TestCase
{
    public function test_reply_message_instance()
    {
        $this->assertInstanceOf(ReplyMessage::class, Bot::reply('token'));
    }

    public function test_reply_message()
    {
        $this->mock(MessagingApiApi::class, function ($mock) {
            $mock->shouldReceive('replyMessage')
                ->once()
                ->andReturn(new ReplyMessageResponse);
        });

        Bot::reply('token')
            ->message(new TextMessage(['text' => 'test', 'type' => MessageType::TEXT]));
    }

    public function test_reply_text()
    {
        $this->mock(MessagingApiApi::class, function ($mock) {
            $mock->shouldReceive('replyMessage')
                ->andReturn(new ReplyMessageResponse)
                ->once();
        });

        Bot::reply('token')->text('test');
    }

    public function test_reply_text_with()
    {
        $this->mock(MessagingApiApi::class, function ($mock) {
            $mock->shouldReceive('replyMessage')
                ->andReturn(new ReplyMessageResponse)
                ->once();
        });

        Bot::reply('token')
            ->withQuickReply(m::mock(QuickReply::class))
            ->withSender('name', 'icon')
            ->text('a', 'b', 'c');
    }

    public function test_reply_sticker()
    {
        $this->mock(MessagingApiApi::class, function ($mock) {
            $mock->shouldReceive('replyMessage')
                ->andReturn(new ReplyMessageResponse)
                ->once();
        });

        Bot::reply('token')->sticker(1, 2);
    }
}
