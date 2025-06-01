<?php

namespace Tests\Notifications;

use Illuminate\Notifications\AnonymousNotifiable;
use Revolution\Line\Facades\Bot;
use Revolution\Line\Notifications\LineChannel;
use Tests\Notifications\Fixtures\LineNotificationStub;
use Tests\Notifications\Fixtures\TestNotifiableStub;
use Tests\TestCase;

class LineChannelTest extends TestCase
{
    public function test_line_channel()
    {
        Bot::shouldReceive('pushMessage')->once();

        $channel = new LineChannel;

        $notifiable = (new AnonymousNotifiable)
            ->route('line', 'test');

        $channel->send($notifiable, new LineNotificationStub('test'));
        $channel->send(new TestNotifiableStub, new LineNotificationStub('test'));
    }
}
