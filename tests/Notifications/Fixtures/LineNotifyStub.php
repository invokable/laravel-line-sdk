<?php

namespace Tests\Notifications\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Revolution\Line\Notifications\LineNotifyChannel;
use Revolution\Line\Notifications\LineNotifyMessage;

/**
 * @deprecated
 */
class LineNotifyStub extends Notification
{
    use Queueable;

    protected string $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return [
            LineNotifyChannel::class,
        ];
    }

    public function toLineNotify(mixed $notifiable): LineNotifyMessage
    {
        return LineNotifyMessage::create($this->message);
    }
}
