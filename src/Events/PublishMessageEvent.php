<?php

namespace Celysium\MessageBroker\Events;

use Celysium\MessageBroker\Message;
use Illuminate\Foundation\Events\Dispatchable;

class PublishMessageEvent
{
    use Dispatchable;

    public Message $message;
    public $ack = null;
    public $nack = null;
    public string $driver;

    public function __construct(Message $message, $ack = null, $nack = null, string $driver = null)
    {
        $this->message      = $message;
        $this->ack        = $ack;
        $this->nack       = $nack;
        $this->driver     = $driver;
    }

    public function ack(callable $callback): PublishMessageEvent
    {
        $this->ack = $callback;
        return $this;
    }

    public function nack(callable $callback): PublishMessageEvent
    {
        $this->nack = $callback;
        return $this;
    }

    public function driver(string $driver): PublishMessageEvent
    {
        $this->driver = $driver;
        return $this;
    }
}
