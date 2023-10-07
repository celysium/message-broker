<?php

namespace Celysium\MessageBroker\Drivers;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Events\IncomingMessageEvent;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements MessageBrokerInterface
{
    public function send(string $event, array $data): void
    {
        $source = env('MICROSERVICE_SLUG', 'none');
        $queue = config('message-broker.rabbitmq.queue');
        $host = config('message-broker.rabbitmq.host');
        $port = config('message-broker.rabbitmq.port');
        $user = config('message-broker.rabbitmq.user');
        $password = config('message-broker.rabbitmq.password');
        $exchange = config('message-broker.rabbitmq.exchange');
        $key = config('message-broker.rabbitmq.exchange_key');

        $data = json_encode(compact('source', 'event', 'data'));

        $connection = new AMQPStreamConnection($host, $port, $user, $password);

        $channel = $connection->channel();
        $channel->exchange_declare($exchange, 'fanout', false, true, false);
        $channel->queue_declare($queue, false, true, false, false);
        $channel->queue_bind($queue, $exchange, $key);
        $message = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($message, $exchange, $key);
        $channel->close();
        $connection->close();
    }

    public function listen(): void
    {
        $queue = config('message-broker.rabbitmq.queue');
        $host = config('message-broker.rabbitmq.host');
        $port = config('message-broker.rabbitmq.port');
        $user = config('message-broker.rabbitmq.user');
        $password = config('message-broker.rabbitmq.password');
        $exchange = config('message-broker.rabbitmq.exchange');

        $connection = new AMQPStreamConnection($host, $port, $user, $password);

        $channel = $connection->channel();
        $channel->exchange_declare($exchange, 'fanout', false, true, false);
        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_consume($queue, '', false, true, false, false, function ($message) {
            echo ' [x] Received ', $message->body, "\n";
            $messageBody = json_decode($message->body, true);
            event(new IncomingMessageEvent($messageBody['event'], $messageBody['data'], $messageBody['source']));
        });

        echo "Waiting for new message on $queue", " \n";

        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();
    }
}
