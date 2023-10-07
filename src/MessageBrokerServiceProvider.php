<?php

namespace Celysium\MessageBroker;

use Celysium\MessageBroker\Console\Commands\ListenerCommand;
use Celysium\MessageBroker\Events\SendMessageEvent;
use Celysium\MessageBroker\Listeners\SendMessageListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class MessageBrokerServiceProvider extends ServiceProvider
{
    public function register()
    {
        Event::listen(SendMessageEvent::class, SendMessageListener::class);

        $this->app->bind('message-broker', function ($app) {
            return new MessageBroker($app);
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/message-broker.php', 'message-broker');

        $this->publishes([
            __DIR__ . '/../config/message-broker.php' => config_path('message-broker.php'),
        ], 'config');

        $this->commands(ListenerCommand::class);
    }

    public function boot()
    {
        $queue = config('message-broker.rabbitmq.queue');
        $host = config('message-broker.rabbitmq.host');
        $port = config('message-broker.rabbitmq.port');
        $user = config('message-broker.rabbitmq.user');
        $password = config('message-broker.rabbitmq.password');
        $exchange = config('message-broker.rabbitmq.exchange');
        $key = config('message-broker.rabbitmq.exchange_key');

        $connection = new AMQPStreamConnection($host, $port, $user, $password);

        $channel = $connection->channel();
        $channel->exchange_declare($exchange, 'fanout', false, true, false);
        $channel->queue_declare($queue, false, true, false, false);
        $channel->queue_bind($queue, $exchange, $key);
        $channel->close();
        $connection->close();
    }
}
