<?php

namespace Celysium\MessageBroker\Contracts;

use Celysium\MessageBroker\Message;

interface MessageBrokerInterface
{
    public function publish(Message $message, callable $ack = null, callable $nack = null): void;
    public function batch(array $messages, callable $ack = null, callable $nack = null);
    public function transaction(array $messages);
    public function consume(): void;
}