<?php

namespace Celysium\MessageBroker;


class Message
{
    private string $event;
    private array $data;
    private string $body;
    private string $receiver;
    public function __construct(string $event, array $data, string $receiver = null)
    {
        $this->event = $event;
        $this->data = $data;
        $this->receiver = $receiver;
    }

    public static function make(string $event, array $data, string $receiver = null): Message
    {
        $message = new self($event, $data, $receiver);
        $message->body = json_encode(compact('event', 'data'));
        return $message;
    }

    public static function resolve(string $body): Message
    {
        $message = json_decode($body, true);
        $message = new self($message['event'], $message['data']);
        $message->body = $body;
        return $message;
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
        return $this->body;
    }

    public function getReceiver(): string
    {
        return $this->receiver;
    }
}
