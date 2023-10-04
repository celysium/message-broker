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
        $source = env('MICROSERVICE_SLUG', 'none');

        $data = json_encode(compact('source', 'event', 'data'));

        $connection = new AMQPStreamConnection(config('message-broker.rabbitmq.host'), config('message-broker.rabbitmq.port'), config('message-broker.rabbitmq.user'), config('message-broker.rabbitmq.password'));

        $channel = $connection->channel();
        $channel->exchange_declare('test_exchange', 'direct', false, false, false);
        $channel->queue_declare(config('message-broker.rabbitmq.queue'), false, true, false, false);
        $channel->queue_bind(config('message-broker.rabbitmq.queue'), 'test_exchange', 'test_key');
        $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
        $channel->basic_publish($msg, 'test_exchange', 'test_key');
        $channel->close();
        $connection->close();

        return true;
    }

    public function listen()
    {
        $connection = new AMQPStreamConnection(config('message-broker.rabbitmq.host'), config('message-broker.rabbitmq.port'), config('message-broker.rabbitmq.user'), config('message-broker.rabbitmq.password'));


        $channel = $connection->channel();
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";

            $messageBody = json_decode($msg->body, true);

            event(new IncomingMessageEvent($messageBody['event'], $messageBody['data'], $messageBody['source']));

        };
        $channel->queue_declare(config('message-broker.rabbitmq.queue'), false, true, false, false);
        $channel->basic_consume(config('message-broker.rabbitmq.queue'), '', false, true, false, false, $callback);
        echo 'Waiting for new message on test_queue', " \n";
        while ($channel->is_consuming()) {
            $channel->wait();
        }
        $channel->close();
        $connection->close();

        return true;
    }
}
