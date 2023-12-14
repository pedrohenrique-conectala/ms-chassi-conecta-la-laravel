<?php

namespace Conectala\Components\Managers\Amqp\RabbitMQ;

use Conectala\Components\Managers\Amqp\AmqpConfiguration;
use Conectala\Components\Managers\Amqp\AmqpManagerConfigurationContract;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class RabbitMQManagerConfiguration implements AmqpManagerConfigurationContract
{
    protected Client $client;

    protected array $configurationDefinition = [];

    protected array $exchanges;
    protected array $queues = [];
    protected array $bindings;

    public function __construct(protected AmqpConfiguration $amqpConfigContext)
    {
        $this->configurationDefinition = $this->amqpConfigContext->getProperty('definitions');
        $this->client = new Client([
            'base_uri' => $this->amqpConfigContext->getProperty('api_uri'),
            'auth' => [
                $this->amqpConfigContext->getProperty('username'),
                $this->amqpConfigContext->getProperty('password')
            ]
        ]);
    }

    public function handler(): void
    {
        $this->reloadDefinitions();
    }

    public function loadedDefinitions(): array
    {
        if (empty($this->queues)) {
            $this->reloadDefinitions();
        }
        return [
            'configurations' => $this->configurationDefinition
        ];
    }

    protected function reloadDefinitions(): void
    {
        $this->handleExchanges($this->configurationDefinition);
        foreach ($this->configurationDefinition['resources'] ?? [] as $k => $resource) {
            if (!($resource['enabled'] ?? false)) continue;
            [$mappedQueues, $mappedBindings] = $this->mapQueuesAndBingings($resource);
            $queues = $this->handleQueues($mappedQueues);
            $this->handleBindings($mappedBindings);
            if (!empty($queues)) {
                $this->configurationDefinition['resources'][$k]['queues'] = $queues;
            }
        }
    }

    protected function mapQueuesAndBingings(array $resource = []): array
    {
        $mappedQueues = [];
        $mappedBindings = [];

        foreach ($resource['consumers'] ?? [] as $consumer) {
            $queueName = sprintf("%s.%s", strtolower($consumer['tenant']), str_slug($resource['name'], '_'));
            $mappedQueues[] = [
                'name' => $queueName,
                'is_default' => false,
                'tenant' => $consumer['tenant']
            ];
            foreach ($resource['events'] ?? [] as $event) {
                if (!($event['enabled'] ?? false)) continue;
                $mappedBindings[] = [
                    'destination' => $queueName,
                    'routing_key' => [
                        'params' => [
                            'tenant' => strtolower($consumer['tenant']),
                            'event' => $event['event']
                        ]
                    ],
                    'is_default' => false
                ];
            }
        }
        return [$mappedQueues, $mappedBindings];
    }

    protected function handleExchanges(array $definitions = [])
    {
        $exchanges = $this->amqpConfigContext->getProperty('exchanges');
        foreach ($exchanges as $exchange) {
            if ($exchange['is_default'] ?? false) continue;
            $default = current(array_filter($exchanges, function ($item) use ($exchange) {
                return ($item['is_default'] ?? false) && ($item['type'] ?? '') === ($exchange['type'] ?? '');
            })) ?: [];
            if (empty($default)) continue;
            $exchange = array_merge($default, $exchange);
            $this->updateDefinition("/api/exchanges/%2f/{$exchange['name']}", $exchange);
        }
    }

    protected function handleQueues(array $mappedQueues = []): array
    {
        $configQueues = $this->amqpConfigContext->getProperty('queues');
        $defaultQueue = current(array_filter($configQueues, function ($item) {
            return $item['is_default'] ?? false;
        })) ?: [];
        foreach ($mappedQueues as $k => $mappedQueue) {
            $mappedQueues[$k] = array_merge($defaultQueue, $mappedQueue);
        }
        $queues = [];
        foreach ($mappedQueues as $queue) {
            if ($queue['is_default'] ?? false) continue;
            $queues[] = $queue;
            $this->updateDefinition("/api/queues/%2f/{$queue['name']}", $queue);
        }
        $this->queues = array_merge($this->queues, $queues);
        return $queues;
    }

    protected function handleBindings(array $mappedBindings = [])
    {
        $configBindings = $this->amqpConfigContext->getProperty('bindings');
        $defaultBindings = array_filter($configBindings, function ($item) {
            return $item['is_default'] ?? false;
        });
        $defaultBinding = array_merge(...$defaultBindings);
        foreach ($mappedBindings as $k => $mappedBinding) {
            $mappedBinding['routing_key'] = $this->discoverBindingRoutingKey($defaultBinding['routing_key']['pattern'], $mappedBinding['routing_key']['params']);
            $mappedBindings[$k] = array_merge($defaultBinding, $mappedBinding);
        }
        foreach ($mappedBindings as $binding) {
            if ($binding['is_default'] ?? false) continue;
            $this->createDefinition("/api/bindings/%2f/e/{$binding['source']}/q/{$binding['destination']}", $binding);
        }
    }

    protected function discoverBindingRoutingKey(string $pattern, array $routingKeyParams): string
    {
        $bindingRoutingKey = $pattern;
        foreach ($routingKeyParams as $name => $value) {
            $bindingRoutingKey = str_replace("{{$name}}", $value, $bindingRoutingKey);
        }
        return $bindingRoutingKey;
    }

    protected function existsDefinition(string $uri): bool
    {
        try {
            $response = $this->client->get($uri);
            Log::info(__CLASS__ . ":" . __FUNCTION__, [
                $uri,
                $response->getStatusCode(),
                $response->getBody()->getContents()
            ]);
            if ($response->getStatusCode() === 200) return true;
        } catch (\Throwable $e) {
            if ($e->getCode() === 404) return false;
        }
        return false;
    }

    protected function createDefinition(string $uri, array $data): bool
    {
        try {
            $response = $this->client->post($uri, ['json' => $data]);
            Log::info(__CLASS__ . ":" . __FUNCTION__, [
                $uri,
                json_encode($data),
                $response->getStatusCode(),
                $response->getBody()->getContents()
            ]);
            if ($response->getStatusCode() === 201) return true;
        } catch (\Throwable $e) {
            Log::info(__CLASS__ . ":" . __FUNCTION__, [
                $uri,
                json_encode($data),
                $e->getCode(), $e->getMessage()
            ]);
        }
        return false;
    }

    protected function updateDefinition(string $uri, array $data): bool
    {
        try {
            $response = $this->client->put($uri, ['json' => $data]);
            Log::info(__CLASS__ . ":" . __FUNCTION__, [
                $uri,
                json_encode($data),
                $response->getStatusCode(),
                $response->getBody()->getContents()
            ]);
            if ($response->getStatusCode() === 204) return true;
        } catch (\Throwable $e) {
            Log::info(__CLASS__ . ":" . __FUNCTION__, [
                $uri,
                json_encode($data),
                $e->getCode(), $e->getMessage()
            ]);
        }
        return false;
    }
}
