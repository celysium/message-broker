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

        $data = compact('source', 'event', 'data');

        $connection = new AMQPStreamConnection(config('Rabbitmq.host'), config('Rabbitmq.port'), config('Rabbitmq.user'), config('Rabbitmq.password'));
        $channel = $connection->channel();

        $channel->queue_declare(config('Rabbitmq.queue'), false, false, false, false);

        $message = new AMQPMessage($data);
        $channel->basic_publish($message, '', config('Rabbitmq.queue'));

        $channel->close();
        $connection->close();

        return true;
    }

    public function consumer()
    {
        $connection = new AMQPStreamConnection(config('Rabbitmq.host'), config('Rabbitmq.port'), config('Rabbitmq.user'), config('Rabbitmq.password'));
        $channel = $connection->channel();

        $channel->queue_declare(config('Rabbitmq.queue'), false, false, false, false);

        $channel->basic_consume(config('Rabbitmq.queue'), '', false, true, false, false, function ($message) {
            $messageBody = $message->body;

            event(new IncomingMessageEvent($messageBody['event'], $messageBody['data'], $messageBody['source']));
        });

        while ($channel->is_open()) {
            $channel->wait();

        }
        return true;
    }
}