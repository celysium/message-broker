<?php

namespace Celysium\MessageBroker\Console\Commands;

use Celysium\MessageBroker\Facades\MessageBroker;
use Illuminate\Console\Command;

class KafkaConsumerCommand extends Command
{
    protected $signature = 'kafka:create-consume';

    protected $description = 'Create Kafka consumer';

    public function handle(): void
    {
        MessageBroker::driver()->consumer();
    }
}