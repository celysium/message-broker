<?php

namespace Celysium\MessageBroker;

use Celysium\MessageBroker\Console\Commands\ConsumeCommand;
use Celysium\MessageBroker\Events\PublishMessageEvent;
use Celysium\MessageBroker\Listeners\PublishMessageListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class MessageBrokerServiceProvider extends ServiceProvider
{
    public function register()
    {
        Event::listen(PublishMessageEvent::class, PublishMessageListener::class);

        $this->app->bind('message-broker', function ($app) {
            return new MessageBroker($app);
        });

        $this->mergeConfigFrom(__DIR__ . '/../config/message-broker.php', 'message-broker');

        $this->publishes([
            __DIR__ . '/../config/message-broker.php' => config_path('message-broker.php'),
        ], 'config');

        $this->commands(ConsumeCommand::class);
    }
}
