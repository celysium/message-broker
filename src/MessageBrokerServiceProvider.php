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
}
