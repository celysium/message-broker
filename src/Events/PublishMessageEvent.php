<?php

namespace Celysium\MessageBroker\Events;

use Illuminate\Foundation\Events\Dispatchable;

class PublishMessageEvent
{
    use Dispatchable;

    public string $event;
    public array $data;
    public $ack = null;
    public $nack = null;
    public string $driver;

    public function __construct(string $event, array $data, $ack = null, $nack = null, string $driver = null)
    {
        $this->event  = $event;
        $this->data   = $data;
        $this->ack    = $ack;
        $this->nack   = $nack;
        $this->driver = $driver;
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
