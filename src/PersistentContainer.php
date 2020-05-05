<?php


namespace Delight\Box;

use Delight\Box\Exceptions\NotCloneableException;
use PhpParser\Node\Stmt\Continue_;

/**
 * @method static mixed resolve(string $id, array $with = [])
 * @method static Container bind(string $id, $value)
 * @method static mixed resolveMethod(string $class, string $method, array $with = [])
 * @method static mixed resolveClosure(\Closure $closure, array $with = [])
 * @method static Container singleton(string $class, \Closure $resolver)
 * @method static bool bound(string $id)
 */
class PersistentContainer
{
    private static ?Container $uniqueInstance = null;

    /**
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     */
    public static function __callStatic(string $name, array $arguments)
    {
        return static::getInstance()->{$name}(...$arguments);
    }

    public static function getInstance(): Container
    {
        if (self::$uniqueInstance === null) {
            self::$uniqueInstance = new Container();
        }

        return self::$uniqueInstance;
    }

    public static function clear(): Container
    {
        self::$uniqueInstance = new Container;

        return self::$uniqueInstance;
    }
}
