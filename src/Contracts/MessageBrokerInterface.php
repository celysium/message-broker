<?php

namespace Celysium\MessageBroker\Contracts;

interface MessageBrokerInterface
{
    public function publish(string $event, array $data, callable $ack = null, callable $nack = null): void;

    public function consume(): void;
}
