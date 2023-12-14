<?php

namespace Conectala\Components\Publishers\Amqp\RabbitMQ;

use Conectala\Components\Managers\Amqp\RabbitMQ\RabbitMQConfiguration;
use Conectala\Components\Publishers\PublisherContract;
use Bschmitt\Amqp\Amqp;
use Bschmitt\Amqp\Message;
use Illuminate\Support\Facades\Log;

abstract class RabbitMQPublisher implements PublisherContract
{
    const DELIVERY_MODE_TRANSIENT = 1;
    const DELIVERY_MODE_PERSISTENT = 2;

    protected Amqp $amqp;

    protected array $messageProperties = [];
    protected array $publishProperties = [];

    public function __construct(protected RabbitMQConfiguration $rabbitMQConfiguration, mixed ...$args)
    {
        $this->amqp = new Amqp();
        $this->prepare();
    }

    protected function prepare()
    {
        $exchanges = $this->rabbitMQConfiguration->getProperty('exchanges') ?? [];
        foreach ($exchanges as $exchange) {
            if (in_array(get_class($this), $exchange['use_in'] ?? [])) {
                $this->publishProperties = [
                    'exchange_type' => $exchange['type'],
                    'exchange' => $exchange['name']
                ];
                break;
            }
        }
        if (empty($this->publishProperties)) {
            foreach ($exchanges as $exchange) {
                if ($exchange['is_default'] ?? false) {
                    $this->publishProperties = [
                        'exchange_type' => $exchange['type'],
                        'exchange' => $exchange['name']
                    ];
                    break;
                }
            }
        }
    }

    protected function getMessageProperties(): array
    {
        return array_merge([
            'content_type' => 'application/json',
            'delivery_mode' => RabbitMQPublisher::DELIVERY_MODE_PERSISTENT
        ], $this->messageProperties);
    }

    protected function getPublishProperties(): array
    {
        return array_merge([
            'exchange_type' => 'topic',
            'exchange' => 'amq.topic'
        ], $this->publishProperties);
    }

    protected abstract function resourceRoutingKey(): string;

    protected function retrieveRoutingKey(): string
    {
        return $this->discoverRoutingKey($this->resourceRoutingKey());
    }

    protected function discoverRoutingKey(string $routingKey): string
    {
        $routingKeyParams = $this->rabbitMQConfiguration->getProperty('routing_key')['params'] ?? [];
        foreach ($routingKeyParams as $name => $value) {
            $routingKey = str_replace("{{$name}}", strtolower($value), $routingKey);
        }
        return $routingKey;
    }

    public function publish(mixed $data): void
    {
        try {
            $result = $this->amqp->publish($this->retrieveRoutingKey(), new Message($data->toJson(), $this->getMessageProperties()), $this->getPublishProperties());
        } catch (\Throwable $e) {
            $result = $e->getMessage();
        }
        Log::info((new \ReflectionClass($this))->getShortName(), [
            'destination' => array_merge([
                'amqp' => 'RabbitMQ',
                'routing_key' => self::retrieveRoutingKey()
            ], $this->getPublishProperties()),
            'message' => array_merge(['content' => $data->toJson()], $this->getMessageProperties()),
            'result' => $result
        ]);
    }
}
