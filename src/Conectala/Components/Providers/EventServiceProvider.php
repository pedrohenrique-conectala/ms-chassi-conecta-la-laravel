<?php

namespace Conectala\Components\Providers;

use Conectala\Components\Configurations\ApplicationConfiguration;
use Conectala\Components\Configurations\TenantConfiguration;
use Conectala\Components\Events\EventApplication;
use Conectala\Components\Listeners\EventApplicationListener;
use Conectala\MultiTenant\TenantManager;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];

    public function register()
    {
        parent::register();
        EventApplicationListener::register(new EventApplication([
            TenantManager::class,
            'setTenant',
            'after'
        ], function (mixed ...$args) {
            $tenant = $args[0] ?? null;
            ApplicationConfiguration::instance(null, TenantConfiguration::params([
                'tenant' => $tenant->tenant ?? null
            ]))::load();
        }));
    }

    public function boot()
    {
        parent::boot();
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
