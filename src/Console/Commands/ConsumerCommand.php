<?php

namespace Celysium\MessageBroker\Console\Commands;

use Celysium\MessageBroker\Facades\MessageBroker;
use Illuminate\Console\Command;

class ConsumerCommand extends Command
{
    protected $signature = 'message-broker:consume {--driver=}';

    protected $description = 'Create message broker consumer';

    public function handle(): void
    {
        $driver = $this->option('driver') ?: config('MESSAGE_BROKER_DEFAULT_DRIVER');

        MessageBroker::driver($driver)->consumer();
    }
}