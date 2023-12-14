<?php

namespace Conectala\Components\Configurations;

use Conectala\Components\Managers\Amqp\RabbitMQ\RabbitMQConfiguration;
use Conectala\Components\Managers\Http\HttpConfiguration;
use Conectala\Components\PubSubResolver;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class TenantConfiguration extends RepositoryConfiguration
{
    protected array $settings = [];
    protected array $params = [];

    public static function params(?array $params = null): mixed
    {
        if (!\app()->runningInConsole()) {
            $params = [
                'marketplace' => getMarketplaceRequest(),
                'tenant' => getTenantRequest(),
                'store_id' => getStoreRequest(),
                'platform' => getPlatformRequest()
            ];
        }
        static::instance()->params = $params ?? static::instance()->params;
        return static::instance();
    }

    protected function init(): void
    {
        $params = static::instance()->params;
        static::instance()::merge(HttpConfiguration::REPOSITORY_KEY, [
            'use' => PubSubResolver::PUBSUB_SELLERCENTER,
            'properties' => [
                PubSubResolver::PUBSUB_SELLERCENTER => [
                    'base_uri' => static::setting('conecta_la_api_url'),
                    'path' => [
                        'params' => $params
                    ],
                ]
            ]
        ]);
        static::instance()::merge(RabbitMQConfiguration::REPOSITORY_KEY, [
                'properties' => [
                    PubSubResolver::PUBSUB_RABBITMQ => [
                        'routing_key' => [
                            'params' => $params
                        ]
                    ]
                ]
            ]
        );
        if ($this->chainedDependency instanceof RepositoryConfiguration) {
            $this->chainedDependency->init();
        }
    }

    protected static function setting(string $name): mixed
    {
        if (empty(static::instance()->settings[$name] ?? null)) {
            $setting = App::make('SettingRepository')->getByName($name);
            if (!($setting->name ?? null)) return null;
            static::instance()->settings[$setting->name] = $setting->value;
        }
        return static::instance()->settings[$name] ?? null;
    }

    public static function instance(?string $className = null, ?RepositoryConfiguration $chainedDependency = null): mixed
    {
        $className = $className ?? TenantConfiguration::class;
        if (!isset(static::$instances[$className])
            || static::$instances[$className] === null) {
            if (class_exists('\App\Components\Configurations\TenantConfiguration')) {
                $class = '\App\Components\Configurations\TenantConfiguration';
                return parent::instance($className, new $class(Config::getFacadeRoot()));
            }
        }
        return parent::instance($className);
    }
}
