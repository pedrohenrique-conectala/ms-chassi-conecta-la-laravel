<?php

namespace Conectala\Components\Providers;

use Conectala\Components\Services\ManagerTenantService;
use Conectala\MultiTenant\Repositories\SettingRepository;
use Conectala\MultiTenant\Repositories\TenantClientRepository;
use Conectala\MultiTenant\TenantFacade;
use Conectala\MultiTenant\TenantManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('TenantRepository', function () {
            return new TenantClientRepository();
        });
        $this->app->bind(TenantClientRepository::class, function () {
            return new TenantClientRepository();
        });
        $this->app->bind('SettingRepository', function () {
            return new SettingRepository();
        });
        $this->app->bind(ManagerTenantService::class, function ($app, $parameters) {
            return new ManagerTenantService(App::make(TenantClientRepository::class), App::make(TenantManager::class));
        });
    }

    public function boot()
    {
    }
}
