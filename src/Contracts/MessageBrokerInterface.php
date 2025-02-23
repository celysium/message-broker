<?php

namespace Celysium\MessageBroker\Contracts;

use Celysium\MessageBroker\Message;

interface MessageBrokerInterface
{
    public function publish(Message $message, callable $ack = null, callable $nack = null): void;

    public function consume(): void;
}
