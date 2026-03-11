<?php

namespace Revolution\Line\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Model\ErrorResponse;
use LINE\Clients\MessagingApi\Model\PushMessageRequest;
use LINE\Clients\MessagingApi\Model\PushMessageResponse;
use LINE\Clients\MessagingApi\Model\ReplyMessageRequest;
use LINE\Clients\MessagingApi\Model\ReplyMessageResponse;
use Revolution\Line\Contracts\BotFactory;
use Revolution\Line\Messaging\BotClient;
use Revolution\Line\Messaging\ReplyMessage;

/**
 * @method static ReplyMessage reply(string $token)
 * @method static Collection parseEvent(Request $request)
 * @method static ReplyMessageResponse|ErrorResponse replyMessage(ReplyMessageRequest $replyMessageRequest, string $contentType = 'application/json')
 * @method static PushMessageResponse|ErrorResponse pushMessage(PushMessageRequest $pushMessageRequest, string $xLineRetryKey = null, string $contentType = 'application/json')
 *
 * @see MessagingApiApi
 * @see BotClient
 */
class Bot extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return BotFactory::class;
    }
}
