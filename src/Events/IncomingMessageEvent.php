<?php

namespace Celysium\MessageBroker\Events;

use Celysium\MessageBroker\Message;
use Illuminate\Foundation\Events\Dispatchable;

class IncomingMessageEvent
{
    use Dispatchable;

    public Message $message;

    public array $data;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }
}
