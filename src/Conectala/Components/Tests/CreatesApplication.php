<?php

namespace Conectala\Components\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Config;
use Laravel\Lumen\Application;

trait CreatesApplication
{
    public function createApplication(): Application
    {
        $this->app->make(Kernel::class)->bootstrap();

        Config::set([
            'database.default' => 'tenant',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.tenant' => []
        ]);
        $clone = Config::get('database.connections.sqlite');
        Config::set('database.connections.tenant', $clone);

        Container::setInstance($this->app);
        return app();
    }
}
