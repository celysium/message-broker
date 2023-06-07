<?php

namespace Celysium\MessageBroker\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class SendMessageEvent
{
    use Dispatchable, SerializesModels;

    public mixed $data;
    public string $event;
    public string $service;

    public function __construct($event, $data, $service = 'none')
    {
        $this->event = $event;
        $this->service = $service;
        $this->data = $data;
    }
}
