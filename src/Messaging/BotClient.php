<?php

namespace Revolution\Line\Messaging;

use BadMethodCallException;
use Illuminate\Support\Traits\Macroable;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use Revolution\Line\Contracts\BotFactory;
use Revolution\Line\Messaging\Concerns\EventParser;
use Revolution\Line\Messaging\Concerns\Replyable;

class BotClient implements BotFactory
{
    use EventParser;
    use Macroable {
        __call as macroCall;
    }
    use Replyable;

    public function __construct(
        protected MessagingApiApi $bot
    ) {
        //
    }

    public function bot(): MessagingApiApi
    {
        return $this->bot;
    }

    public function botUsing(callable|MessagingApiApi $bot): static
    {
        $this->bot = is_callable($bot) ? $bot() : $bot;

        return $this;
    }

    /**
     * Magic call method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     *
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (method_exists($this->bot(), $method)) {
            return $this->bot()->{$method}(...array_values($parameters));
        }

        return $this->macroCall($method, $parameters);
    }
}
