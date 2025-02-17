<?php

namespace Celysium\MessageBroker\Listeners;

use Celysium\MessageBroker\Events\PublishMessageEvent;
use Celysium\MessageBroker\Facades\MessageBroker;

class PublishMessageListener
{
    public function handle(PublishMessageEvent $event): void
    {
        MessageBroker::driver($event->driver)->publish($event->event, $event->data, $event->ack, $event->nack);
    }
}
