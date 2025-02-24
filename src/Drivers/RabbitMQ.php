<?php

namespace Celysium\MessageBroker\Drivers;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Events\IncomingMessageEvent;
use Celysium\MessageBroker\Message;
use Exception;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQ implements MessageBrokerInterface
{
    private object $config;
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;

    public function __construct()
    {
        $this->setConfig();
        $this->connect();
        $this->declare();
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function setConfig(array $config = [])
    {
        $this->config = (object)array_merge(config('message-broker.rabbitmq'), $config);
    }

    private function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config->host,
            $this->config->port,
            $this->config->user,
            $this->config->password,
            $this->config->vhost
        );
    }

    private function disconnect()
    {
        $this->channel->close();
        $this->connection->close();
    }

    private function declare()
    {
        /** @var AMQPChannel $channel */
        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->config->exchange->name, $this->config->exchange->type, false, true, false);
        $this->channel->queue_declare($this->config->queue, false, true, false, false);
        $this->channel->queue_bind($this->config->queue, $this->config->exchange->name, $this->config->exchange->key);
    }

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
     * @param Message $message
     * @param callable|null $ack
     * @param callable|null $nack
     * @return void
     * @throws Exception
     */
    public function publish(Message $message, callable $ack = null, callable $nack = null): void
    {

        $this->channel->confirm_select();
        if ($ack) {
            $this->channel->set_ack_handler($ack);
        }
        if ($nack) {
            $this->channel->set_nack_handler($nack);
        }

        $msg = new AMQPMessage($message->getBody(), ['delivery_mode' => $this->config->message->delivery_mode]);
        $this->channel->basic_publish($msg, $this->config->exchange->name, $message->getReceiver() ?? $this->config->queue);

        $this->channel->wait_for_pending_acks();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function consume(): void
    {
        $callback = function (AMQPMessage $message) {
            echo sprintf("[%s] Received message : %s\n", now(), $message->getBody());

            event(new IncomingMessageEvent(Message::resolve($message->getBody())));
            $message->ack();
        };

        /** @var AMQPChannel $channel */
        $channel->basic_consume($this->config->queue, '', false, false, false, false, $callback);

        echo sprintf("[%s] ready for gat new message : %s\n", now(), $this->config->queue);
        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }

    /**
     * @param Message[] $messages
     * @param callable|null $ack
     * @param callable|null $nack
     * @return void
     * @throws Exception
     */
    public function batch(array $messages, callable $ack = null, callable $nack = null)
    {

        $this->channel->confirm_select();
        if ($ack) {
            $this->channel->set_ack_handler($ack);
        }
        if ($nack) {
            $this->channel->set_nack_handler($nack);
        }

        foreach ($messages as $message) {
            $msg = new AMQPMessage($message->getBody());
            $this->channel->batch_basic_publish($msg, $this->config->exchange->name, $message->getReceiver() ?? $this->config->queue);
        }
        $this->channel->publish_batch();

        $this->channel->wait_for_pending_acks();
    }

    /**
     * @param array $messages
     * @return void
     * @throws Exception
     */
    public function transaction(array $messages)
    {
        $this->channel->tx_select();

        foreach ($messages as $message) {
            $msg = new AMQPMessage($message->getBody(), ['delivery_mode' => $this->config->message->delivery_mode]);
            $this->channel->basic_publish($msg, $this->config->exchange->name, $message->getReceiver() ?? $this->config->queue);
        }

        $this->channel->tx_commit();
    }
}
