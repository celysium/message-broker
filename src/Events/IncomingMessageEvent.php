<?php

namespace Celysium\MessageBroker\Events;

use Illuminate\Foundation\Events\Dispatchable;

class IncomingMessageEvent
{
    use Dispatchable;

    public string $event;

    public array $data;

    public function __construct(string $event, array $data)
    {
        $this->event = $event;
        $this->data = $data;
    }
}
