<?php


namespace Delight\Box;

use Delight\Singleton\Singleton;

/**
 * @method static mixed resolve(string $id, array $with = [])
 * @method static mixed resolveClosure(\Closure $closure, array $with = [])
 * @method static mixed resolveMethod(string $class, string $method, array $with = [])
 * @method static Container bind(string $id, $value)
 * @method static Container singleton(string $id, \Closure $closure)
 * @method static bool bound(string $id)
 * @method static bool singletonBound(string $id)
 *
 */

class PersistentContainer
{
    protected static ?Container $uniqueInstance = null;

    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return self::getInstance()->{$name}(...$arguments);
    }

    public static function getInstance(): Container
    {
        if (self::$uniqueInstance === null) {
            self::$uniqueInstance = new Container;
        }

        return self::$uniqueInstance;
    }
}
