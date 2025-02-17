<?php

namespace Celysium\MessageBroker\Drivers;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Events\IncomingMessageEvent;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements MessageBrokerInterface
{
    /**
     * @param callable $action
     * @throws Exception
     */
    public function exec(callable $action)
    {
        $config = (object)config('message-broker.rabbitmq');

        $connection = new AMQPStreamConnection(
            $config->host,
            $config->port,
            $config->user,
            $config->password,
            $config->vhost
        );

        /** @var AMQPChannel $channel */
        $channel = $connection->channel();
        $channel->exchange_declare($config->exchange->name, $config->exchange->type, false, true, false);
        $channel->queue_declare($config->queue, false, true, false, false);
        $channel->queue_bind($config->queue, $config->exchange->name, $config->exchange->key);

        $action($config, $channel);

        $channel->close();
        $connection->close();
    }

    /**
     * @param string $event
     * @param array $data
     * @param callable|null $ack
     * @param callable|null $nack
     * @return void
     * @throws Exception
     */
    public function publish(string $event, array $data, callable $ack = null, callable $nack = null): void
    {
        $this->exec(function ($config, $channel) use ($event, $data, $ack, $nack) {

            /** @var AMQPChannel $channel */
            $channel->confirm_select();
            if ($ack) {
                $channel->set_ack_handler($ack);
            }
            if ($nack) {
                $channel->set_nack_handler($nack);
            }
            $payload = json_encode(compact('event', 'data'));

            $message = new AMQPMessage($payload, ['delivery_mode' => $config->message->delivery_mode]);
            $channel->basic_publish($message, $config->exchange->name, $config->exchange->key);

            $channel->wait_for_pending_acks();

        });
    }

    /**
     * @return void
     * @throws Exception
     */
    public function consume(): void
    {
        $this->exec(function ($config, $channel) {

            $callback = function (AMQPMessage $message) {
                echo sprintf("[%s] Received message : %s\n", now(), $message->getBody());

                $messageBody = json_decode($message->getBody(), true);
                event(new IncomingMessageEvent($messageBody['event'], $messageBody['data']));
                $message->ack();
            };

            /** @var AMQPChannel $channel */
            $channel->basic_consume($config->queue, '', false, false, false, false, $callback);

            echo sprintf("[%s] ready for gat new message : %s\n", now(), $config->queue);
            while ($channel->is_consuming()) {
                $channel->wait();
            }
        });
    }
}
