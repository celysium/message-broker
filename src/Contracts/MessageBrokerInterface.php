<?php

namespace Celysium\MessageBroker\Contracts;

interface MessageBrokerInterface
{
    public function send(string $event, array $data);

    public function listen();
}
