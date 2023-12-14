<?php

namespace Conectala\CacheWrapper\Wrappers;

use Conectala\CacheWrapper\Handlers\Cache\CacheHandler;
use Conectala\CacheWrapper\Helpers\Cache\Key;
use Illuminate\Support\Facades\Cache as CacheManager;

class Cache
{
    /**
     * @return CacheHandler
     * @throws \Exception
     */
    private static function init(): CacheHandler
    {
        $cacheHandler = new CacheHandler(...func_get_args());
        if (empty($cacheHandler->keys())) {
            throw new \Exception('Cache key not found!');
        }
        return $cacheHandler;
    }

    /**
     * @param string|array $keyMap
     * @param \Closure $closureResult
     * @param \DateTimeInterface|\DateInterval|int|null $expTime
     * @param object|string|null $scope
     * @param string|null $scopeMethod
     * @return mixed
     * @throws \Throwable
     */
    public static function remember(
        string|array                              $keyMap,
        \Closure                                  $closureResult,
        \DateTimeInterface|\DateInterval|int|null $expTime = null,
        object|string|null                        $scope = null,
        string|null                               $scopeMethod = null
    ): mixed
    {
        try {
            $cacheHandler = self::init($keyMap, $closureResult, $scope, $scopeMethod);
            $cachingKey = Key::build($cacheHandler->keys());

            $cachingTags = $cacheHandler->tags();
            $cacheValue = !empty($cachingTags) ? CacheManager::tags($cachingTags)->get($cachingKey) : CacheManager::get($cachingKey);
            if (!is_null($cacheValue)) {
                return $cacheValue;
            }
            try {
                $cacheValue = $closureResult(...[$cachingKey, $cachingTags, $cacheHandler->keys()]); // params to debug
            } catch (\Throwable $e) {
                throw new ClousureResultException($e->getMessage(), $e->getCode(), $e);
            }
            $cacheValue = !empty($cacheValue) ? $cacheValue : (($cacheValue === false || is_numeric($cacheValue)) ? $cacheValue : null);
            $args = [$cachingKey, $cacheValue, $expTime];
            $result = !empty($cachingTags) ? CacheManager::tags($cachingTags)->put(...$args) : CacheManager::put(...$args);
            return $cacheValue;
        } catch (ClousureResultException|\Throwable $e) {
            if ($e instanceof ClousureResultException) throw $e->getPrevious();
        }
        return $closureResult();
    }

    /**
     * @param string|array $keyMap
     * @param \Closure|null $closureResult
     * @param object|string|null $scope
     * @param string|null $scopeMethod
     * @return mixed
     * @throws \Throwable
     */
    public static function forget(
        string|array       $keyMap,
        \Closure|null      $closureResult = null,
        object|string|null $scope = null,
        string|null        $scopeMethod = null
    ): mixed
    {
        try {
            $cacheHandler = self::init(...func_get_args());
            $cachingKey = Key::build($cacheHandler->keys());
            $cachingTags = $cacheHandler->tags();
            $result = !empty($cachingTags)
                ? CacheManager::tags($cachingTags)->pull($cachingKey, fn() => true)
                : CacheManager::pull($cachingKey, fn() => true);
        } catch (\Throwable $e) {
        }
        try {
            return $closureResult instanceof \Closure ? $closureResult() : ($result ?? false);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public static function clear(): bool
    {
        return CacheManager::flush();
    }

}
