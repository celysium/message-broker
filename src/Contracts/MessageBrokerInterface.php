<?php

namespace Celysium\MessageBroker\Contracts;

interface MessageBrokerInterface
{
    public function send(string $event, array $data): void;

    public function listen(): void;
}
