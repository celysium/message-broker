<?php

namespace Celysium\MessageBroker\Drivers;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Events\IncomingMessageEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements MessageBrokerInterface
{
    public function send(string $event, array $data)
    {
        $source = env('MICROSERVICE_SLUG' , 'none');

        $data = json_encode(compact('source', 'event', 'data'));

        $connection = new AMQPStreamConnection(config('message-broker.rabbitmq.host'), config('message-broker.rabbitmq.port'), config('message-broker.rabbitmq.user'), config('message-broker.rabbitmq.password'));
        $channel = $connection->channel();

        $channel->queue_declare(config('message-broker.rabbitmq.queue'), false, false, false, false);

        $message = new AMQPMessage($data);
        $channel->basic_publish($message, '', config('message-broker.rabbitmq.queue'));

        $channel->close();
        $connection->close();

        return true;
    }

    public function listen()
    {
        $connection = new AMQPStreamConnection(config('message-broker.rabbitmq.host'), config('message-broker.rabbitmq.port'), config('message-broker.rabbitmq.user'), config('message-broker.rabbitmq.password'));
        $channel = $connection->channel();

        $channel->queue_declare(config('message-broker.rabbitmq.queue'), false, false, false, false);

        $channel->basic_consume(config('message-broker.rabbitmq.queue'), '', false, true, false, false, function ($message) {
            $messageBody = json_decode($message->body);

            event(new IncomingMessageEvent($messageBody->event, $messageBody->data, $messageBody->source));
        });

        while ($channel->is_open()) {
            $channel->wait();

        }
        return true;
    }
}
