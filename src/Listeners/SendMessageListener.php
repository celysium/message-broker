<?php

namespace Celysium\MessageBroker\Listeners;

use Celysium\MessageBroker\Events\SendMessageEvent;
use Celysium\MessageBroker\Facades\MessageBroker;

class SendMessageListener
{
    public function handle(SendMessageEvent $event): void
    {
        MessageBroker::driver($event->driver)->send($event->event, $event->data);
    }
}
