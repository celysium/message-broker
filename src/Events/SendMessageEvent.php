<?php

namespace Celysium\MessageBroker\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class SendMessageEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public $event, public array $data, public ?string $driver = null)
    {
    }
}
