<?php

namespace Conectala\Components\Managers;

use Conectala\Components\Events\EventPublisherContract;
use Conectala\Components\Managers\Amqp\OracleAQ\OracleAQConfiguration;
use Conectala\Components\Managers\Amqp\RabbitMQ\RabbitMQConfiguration;
use Conectala\Components\Managers\Http\SellerCenter\SellerCenterConfiguration;
use Conectala\Components\Publishers\PublisherContract;
use Conectala\Components\PubSubResolver;
use Conectala\Components\Subscribers\SubscriberContract;

class PubSubManager
{
    protected static array $instance;

    protected PubSubConfiguration $publisherConfiguration;
    protected PubSubConfiguration $subscriberConfiguration;

    private function __construct()
    {
    }

    protected static function pubInstance(?string $publisher = null): static
    {
        if (is_null(static::$instance['pub'][$publisher] ?? null)) {
            static::$instance['pub'][$publisher] = new static();
        }
        return static::$instance['pub'][$publisher];
    }

    protected static function subInstance(?string $subscriber = null): static
    {
        if (is_null(static::$instance['sub'][$subscriber] ?? null)) {
            static::$instance['sub'][$subscriber] = new static();
        }
        return static::$instance['sub'][$subscriber];
    }

    public static function resolvePublisherConfiguration(?string $publisher = null, mixed ...$args): static
    {
        $publisher = $publisher ?? PubSubResolver::publisherReceiver();
        $publisherConfiguration = match ($publisher) {
            PubSubResolver::PUBSUB_RABBITMQ => RabbitMQConfiguration::class,
            PubSubResolver::PUBSUB_ORACLEAQ => OracleAQConfiguration::class,
            default => SellerCenterConfiguration::class
        };
        static::pubInstance($publisher)->publisherConfiguration = new $publisherConfiguration(...$args);
        return static::pubInstance($publisher);
    }

    public static function resolveSubscriberConfiguration(?string $subscriber = null, mixed ...$args): static
    {
        $subscriber = $subscriber ?? PubSubResolver::subscriberProvider();
        $subscriberConfiguration = match ($subscriber) {
            PubSubResolver::PUBSUB_RABBITMQ => RabbitMQConfiguration::class,
            PubSubResolver::PUBSUB_ORACLEAQ => OracleAQConfiguration::class,
            default => SellerCenterConfiguration::class
        };
        static::subInstance($subscriber)->subscriberConfiguration = new $subscriberConfiguration(...$args);
        return static::subInstance($subscriber);
    }

    public function resolvePublisherClassByEvent(EventPublisherContract $eventPublisher, mixed ...$args): PublisherContract
    {
        $publishClass = static::resolveClassByNamespaceList(
            $eventPublisher->getPublisherClass(),
            $this->publisherConfiguration->getPublishersNamespaces() ?? []
        );
        return new $publishClass($this->publisherConfiguration, ...$args);
    }

    public function resolveSubscriberClass(string $className = 'Subscriber', mixed ...$args): SubscriberContract
    {
        $subscriberClass = static::resolveClassByNamespaceList(
            $className ?? 'Subscriber',
            $this->subscriberConfiguration->getSubscribersNamespaces() ?? []
        );
        return new $subscriberClass($this->subscriberConfiguration, ...$args);
    }

    protected static function resolveClassByNamespaceList(string $className, array $namespaceList = []): string
    {
        foreach ($namespaceList as $namespace) {
            $class = "{$namespace}{$className}";
            if (class_exists($class)) return $class;
        }
        return $className;
    }
}
