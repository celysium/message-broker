<?php

namespace Celysium\MessageBroker;


class Message
{
    private string $event;
    private array $data;
    private string $receiver;
    public function __construct(string $event, array $data, string $receiver = null)
    {
        $this->event = $event;
        $this->data = $data;
        $this->receiver = $receiver;
    }

    public static function make(string $event, array $data, string $receiver = null): Message
    {
        return new self($event, $data, $receiver);
    }

    public static function resolve(string $event, string $body): Message
    {
        return new self($event, json_decode($body, true));
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getBody(): string
    {
        return json_encode($this->data);
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }
}
