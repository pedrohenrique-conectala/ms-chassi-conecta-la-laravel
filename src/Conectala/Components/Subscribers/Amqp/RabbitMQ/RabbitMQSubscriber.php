<?php

namespace Conectala\Components\Subscribers\Amqp\RabbitMQ;

use Bschmitt\Amqp\Consumer;
use Conectala\Components\Managers\Amqp\RabbitMQ\RabbitMQConfiguration;
use Conectala\Components\Subscribers\SubscriberContract;
use Bschmitt\Amqp\Amqp;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQSubscriber implements SubscriberContract
{
    protected Amqp $amqp;

    public function __construct(protected RabbitMQConfiguration $rabbitMQConfiguration, mixed ...$args)
    {
        $this->amqp = new Amqp();
    }

    public function consumer(array $resource = []): void
    {
        foreach ($resource['queues'] as $queue) {
            $this->amqp->consume($queue['name'], function (AMQPMessage $message, Consumer $resolver) use ($resource, $queue) {
                try {
                    $routingKey = $message->getRoutingKey();
                    if (!str_starts_with($routingKey, $queue['tenant']))
                        throw new \Exception("Message routingkey {$routingKey} does not match the tenant {$queue['tenant']} of the consumed queue.");
                    $eventHandler = current(array_filter($resource['events'] ?? [], function ($event) use ($routingKey) {
                        return str_ends_with($routingKey, $event['event']);
                    })) ?: [];
                    if (empty($eventHandler)) throw new \Exception("No class found to handle the message matches this routingkey {$routingKey}.");

                    $payload = json_decode($message->body);
                    $payload = is_null($payload) ? (object)[] : $payload;
                    $messageHandler = $eventHandler['consumer'];

                    Log::info((new \ReflectionClass($this))->getShortName() . ' [Processing message]', [
                        'messageHandler' => $messageHandler,
                        'class' => (new \ReflectionClass($this))->getShortName(),
                        //'consumer' => $resource,
                        'queue' => $queue,
                        'message' => $message
                    ]);
                    $payload->tenant = $queue['tenant'];
                    Event::dispatch($messageHandler, [$messageHandler, $payload, function () use ($resolver, $message) {
                        Log::info((new \ReflectionClass($this))->getShortName() . ' [RESOLVE]', [
                            'description' => 'The message was processed successfully. Removing message from the queue...',
                            'message' => $message,
                        ]);
                        $resolver->acknowledge($message);
                        return;
                    }, function () use ($message) {
                        Log::warning((new \ReflectionClass($this))->getShortName() . ' [REJECT]', [
                            'error' => 'An error occurred while processing the message.',
                            'message' => $message,
                        ]);
                        return;
                    }]);
                } catch (\Throwable $e) {
                    $resolver->acknowledge($message);
                    Log::warning('Error processing message', [
                        'class' => (new \ReflectionClass($this))->getShortName(),
                        'exception' => [
                            'message' => $e->getMessage(),
                            'file' => $e->getFile(),
                            'line' => $e->getLine()
                        ],
                        //'consumer' => $resource,
                        'queue' => $queue,
                    ]);
                }
            });
        }
    }
}
