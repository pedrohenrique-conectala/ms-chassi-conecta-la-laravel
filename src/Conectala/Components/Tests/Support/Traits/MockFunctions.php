<?php

namespace Conectala\Components\Tests\Support\Traits;

use phpmock\Mock;
use phpmock\MockBuilder;

trait MockFunctions
{
    protected string $platformRequest = 'vtex';
    protected string $tenantRequest = 'tenant';
    protected string $marketplaceRequest = 'LojasMM';
    protected int $storeRequest = 0;

    protected MockBuilder $mockBuilder;

    public function functionsMocker(): void
    {
        Mock::disableAll();
        $this->mockBuilder = new MockBuilder();

        foreach ($this->getNamespaces() as $namespace) {
            foreach ($this->getFunctions() as $functioName => $closure) {
                $this->mockBuilder->setNamespace($namespace)->setName($functioName)->setFunction(function () use ($functioName, $closure) {
                    return ($closure)();
                })->build()->enable();
            }
        }
    }

    protected function getNamespaces(): array
    {
        return [
            '\\App\\Http\\Resources\\API\\v1',
            '\\App\\Http\\Resources\\API',
            '\\App\\Http\\Resources',
            '\\App\\Http\\Requests',
            '\\App\\Http\\Middleware',
            '\\App\\Http\\Middleware\\Keycloak',
            '\\App\\Services',
            '\\App\\Services\\API',
            '\\App\\Services\\API\\v1',
            '\\App\\Repositories',
            '\\App\\Models',
            '\\App\\Mappers',
            '\\App\\Listeners',
            '\\App\\Factories',
            '\\App\\Events',
            '\\App\\Components\\Configurations',
        ];
    }

    protected function getFunctions(): array
    {
        return [
            'getPlatformRequest' => function () {
                return $this->getPlatformRequest();
            },
            'getTenantRequest' => function () {
                return $this->getTenantRequest();
            },
            'getMarketplaceRequest' => function () {
                return $this->getMarketplaceRequest();
            },
            'getStoreRequest' => function () {
                return $this->getStoreRequest();
            }
        ];
    }

    protected function getPlatformRequest(): string
    {
        return $this->platformRequest;
    }

    protected function getTenantRequest(): string
    {
        return $this->tenantRequest;
    }

    protected function getMarketplaceRequest(): string
    {
        return $this->marketplaceRequest;
    }

    protected function getStoreRequest(): string
    {
        return $this->storeRequest;
    }
}
