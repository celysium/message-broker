<?php

namespace Celysium\MessageBroker;

use Illuminate\Support\ServiceProvider;
class MessageBrokerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('message-broker', function($app) {
            return new MessageBroker();
        });
    }
}