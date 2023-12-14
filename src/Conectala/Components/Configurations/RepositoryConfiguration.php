<?php

namespace Conectala\Components\Configurations;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class RepositoryConfiguration
{
    protected static array|null $instances = null;

    protected function __construct(protected Repository $repository, protected ?RepositoryConfiguration $chainedDependency = null)
    {

    }

    public static function load(): void
    {
        static::instance()->init();
    }

    protected function init(): void
    {
        if ($this->chainedDependency instanceof RepositoryConfiguration) {
            $this->chainedDependency->init();
        }
    }

    public static function instance(?string $className = null, ?RepositoryConfiguration $chainedDependency = null): mixed
    {
        $className = $className ?? static::class;
        if (!isset(static::$instances[$className]) || static::$instances[$className] === null) {
            static::$instances[$className] = new static(Config::getFacadeRoot(), $chainedDependency);
        }
        return static::$instances[$className];
    }

    public static function set(string $repository, array $configurations = []): void
    {
        static::instance()->repository->set($repository, $configurations);
    }

    public static function get(string $repository): array
    {
        return static::instance()->repository->get($repository, []);
    }

    public static function has(string $repository): bool
    {
        return static::instance()->repository->has($repository);
    }

    public static function merge(string $repository, array $configurations = []): void
    {
        if (static::instance()->repository->has($repository)) {
            $repoConfigurations = static::instance()->repository->get($repository);
            $configurations = static::instance()->arrayMergeRecursiveDistinct($repoConfigurations, $configurations);
        }
        static::instance()->repository->set($repository, $configurations);
    }

    public function arrayMergeRecursiveDistinct(array &$original, array &$toMerge)
    {
        $merged = $original;
        foreach ($toMerge as $key => &$value) {
            if(is_array($value) && array_is_list($value) && str_ends_with($key, '.*')){
                $key = str_replace('.*', '', $key);
                $merged[$key] = $value;
            }
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = static::instance()->arrayMergeRecursiveDistinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}
