<?php

namespace Celysium\MessageBroker\Console\Commands;

use Celysium\MessageBroker\Facades\MessageBroker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RabbitMQListenCommand extends Command
{
    protected $signature = 'rabbitmq:listen';

    protected $description = 'Listen and consume messages';

    public function handle(): void
    {
        MessageBroker::driver()->consumer();
    }
}
