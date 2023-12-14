<?php

namespace Conectala\Components\Services;

use Conectala\Components\Configurations\ApplicationConfiguration;
use Conectala\Components\Configurations\TenantConfiguration;
use Conectala\Components\Managers\Amqp\OracleAQ\OracleAQConfiguration;
use Conectala\Components\Managers\Amqp\OracleAQ\OracleAQManagerConfiguration;
use Conectala\Components\Managers\Amqp\RabbitMQ\RabbitMQConfiguration;
use Conectala\Components\Managers\Amqp\RabbitMQ\RabbitMQManagerConfiguration;
use Conectala\Components\Managers\ComponentManagerConfigurationHandler;
use Conectala\Components\Managers\PubSubManager;
use Conectala\Components\PubSubResolver;
use Conectala\MultiTenant\Repositories\TenantClientRepository;
use Conectala\MultiTenant\TenantFacade;
use Conectala\MultiTenant\TenantManager;
use Illuminate\Support\Facades\Log;

class ManagerTenantService
{

    public function __construct(
        protected TenantClientRepository $tenantClientRepository,
        protected TenantManager          $tenantManager
    )
    {
    }

    public function reload(): void
    {
        ApplicationConfiguration::load();
        if (PubSubResolver::publisherReceiver() === PubSubResolver::PUBSUB_ORACLEAQ) {
            ComponentManagerConfigurationHandler::add(new OracleAQManagerConfiguration(new OracleAQConfiguration()));
        } else if (PubSubResolver::publisherReceiver() === PubSubResolver::PUBSUB_RABBITMQ) {
            ComponentManagerConfigurationHandler::add(new RabbitMQManagerConfiguration(new RabbitMQConfiguration()));
        }
        ComponentManagerConfigurationHandler::run();
    }

    public function loadPublishers()
    {
        $this->reload();
    }


    public function loadSubscriberConfigurations(): array
    {
        if (PubSubResolver::subscriberProvider() === PubSubResolver::PUBSUB_ORACLEAQ) {
            ComponentManagerConfigurationHandler::add(new OracleAQManagerConfiguration(new OracleAQConfiguration()));
            return ComponentManagerConfigurationHandler::loadedDefinitions()[OracleAQManagerConfiguration::class] ?? [];
        } else if (PubSubResolver::subscriberProvider() === PubSubResolver::PUBSUB_RABBITMQ) {
            ComponentManagerConfigurationHandler::add(new RabbitMQManagerConfiguration(new RabbitMQConfiguration()));
            return ComponentManagerConfigurationHandler::loadedDefinitions()[RabbitMQManagerConfiguration::class] ?? [];
        }
        return [];
    }

    public function allocateSubscribers(?string $resource = null, ?string $tenant = null): void
    {
        ApplicationConfiguration::load();
        $config = $this->loadSubscriberConfigurations();
        foreach ($config['configurations']['resources'] ?? [] as $src) {
            if (!($src['enabled'] ?? false)) {
                continue;
            }
            $resourceName = str_slug($src['name'], '_');
            if (!is_null($resource) && $resource !== $resourceName) continue;
            $commands = [];

            foreach ($src['consumers'] as $consumer) {
                if (!is_null($tenant) && $tenant !== $consumer['tenant']) continue;
                $commands[$consumer['tenant']][] = sprintf("php artisan tenant:subscribers --resource=%s --tenant=%s >/dev/null", $resourceName, $consumer['tenant']);
            }

            if (!empty($commands)) {
                foreach ($commands as $tenant => $command) {
                    $shellCommands = implode(' && ', array_merge([sprintf("cd %s", base_path())], $command));
                    $shellCommands = sprintf("%s %s", $shellCommands, '&');
                    exec($shellCommands, $output);
                    Log::info((new \ReflectionClass($this))->getShortName(), [
                        'description' => "Executing command to consume the '{$tenant}' client '{$resourceName}' queue",
                        'command' => $shellCommands,
                        'output' => $output
                    ]);
                }
            }
        }
    }

    public function allocateTenantSubscribers(string $tenant, ?string $resource = null)
    {
        $checkTentant = $this->tenantClientRepository->getByTenant($tenant);
        if (!$checkTentant->exists) {
            throw new \Exception("The tenant '{$tenant}' doesn't exist in this database");
        }
        $this->tenantManager->setTenant($checkTentant);
        $subscriberConfigurations = $this->loadSubscriberConfigurations();
        $configurations = $subscriberConfigurations['configurations'] ?? [];
        $resources = array_filter($configurations['resources'] ?? [], function ($item) use ($tenant, $resource) {
            if (!$item['enabled']) return false;
            $consumers = array_filter($item['consumers'] ?? [], function ($consumer) use ($tenant) {
                return strcasecmp($consumer['tenant'], $tenant) === 0;
            });
            if (empty($consumers)) return false;
            return is_null($resource) ? true : (
            strcasecmp(str_slug($item['name']), $resource) === 0 ? true :
                strcasecmp(str_singular(str_slug($item['name'], '_')), $resource) === 0
            );
        });

        foreach ($resources as $resource) {
            PubSubManager::resolveSubscriberConfiguration(PubSubResolver::subscriberProvider())
                ->resolveSubscriberClass()->consumer($resource);
        }

    }

}
