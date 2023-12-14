<?php

namespace Conectala\Components\Configurations;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ApplicationConfiguration extends RepositoryConfiguration
{
    protected static mixed $instance = null;

    const REPOSITORY_KEY = 'app';

    protected function init(): void
    {
        static::instance()::merge(ApplicationConfiguration::REPOSITORY_KEY, [
            'namespace' => App::getNamespace()
        ]);
        if ($this->chainedDependency instanceof RepositoryConfiguration) {
            $this->chainedDependency->init();
        }
    }

    public static function instance(?string $className = null, ?RepositoryConfiguration $chainedDependency = null): mixed
    {
        $className = $className ?? ApplicationConfiguration::class;
        if (!isset(static::$instances[$className])
            || static::$instances[$className] === null) {
            if (class_exists('\App\Components\Configurations\ApplicationConfiguration')) {
                $class = '\App\Components\Configurations\ApplicationConfiguration';
                return parent::instance($className, new $class(Config::getFacadeRoot(), $chainedDependency));
            }
        }
        return parent::instance($className);
    }
}
