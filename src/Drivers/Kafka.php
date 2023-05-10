<?php

namespace Celysium\MessageBroker\Drivers;

use Celysium\MessageBroker\Contracts\MessageBrokerInterface;
use Celysium\MessageBroker\Events\IncomingMessageEvent;
use Junges\Kafka\Facades\Kafka as KafkaDriver;
use Junges\Kafka\Message\Message;
use Junges\Kafka\Contracts\KafkaConsumerMessage;

class Kafka implements MessageBrokerInterface
{
    public function send(string $event, array $data)
    {
        $source = env('MICROSERVICE_SLUG' , 'none');

        $message = new Message(
            body: compact('source', 'event', 'data'),
        );

        $producer = KafkaDriver::publishOn(config('message-broker.kafka.topic'))
            ->withMessasge($message)
            ->withDebugEnabled(config('message-broker.kafka.debug'));

        return $producer->send();
    }

    public function listen()
    {
        $consumer = KafkaDriver::createConsumer(config('Kafka.consumer'))
            ->withHandler(function(KafkaConsumerMessage $message) {
                $messageBody = $message->getBody();

                event(new IncomingMessageEvent($messageBody['event'], $messageBody['data'], $messageBody['source']));
            })
            ->build();

        return $consumer->consume();
    }
}
