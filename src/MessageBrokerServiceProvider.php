<?php

namespace Celysium\MessageBroker;

use Celysium\MessageBroker\Console\Commands\ListenerCommand;
use Illuminate\Support\ServiceProvider;
class MessageBrokerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('message-broker', function($app) {
            return new MessageBroker($app);
        });

        $this->mergeConfigFrom( __DIR__ . '/../config/message-broker.php', 'message-broker');

        $this->publishes([
            __DIR__ . '/../config/message-broker.php' => config_path('message-broker.php'),
        ], 'config');

        $this->commands(ListenerCommand::class);
    }
}
