<?php

namespace Celysium\MessageBroker\Console\Commands;

use Celysium\MessageBroker\Facades\MessageBroker;
use Illuminate\Console\Command;

class ListenerCommand extends Command
{
    protected $signature = 'message-broker:consume {--driver=}';

    protected $description = 'Create message broker listener';

    public function handle(): void
    {
        $driver = $this->option('driver') ?: config('message-broker.default');

        MessageBroker::driver($driver)->listen();
    }
}
