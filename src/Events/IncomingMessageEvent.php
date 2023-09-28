<?php

namespace Celysium\MessageBroker\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class IncomingMessageEvent
{
    use Dispatchable, SerializesModels;


    public function __construct(public string $event, public array $data, public string $service = 'none')
    {
    }
}
