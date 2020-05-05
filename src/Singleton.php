<?php


namespace Delight\Box;

use Delight\Box\Exceptions\NotCloneableException;

trait Singleton
{
    private static ?Container $uniqueInstance = null;

    public static function getInstance(): Container
    {
        if (self::$uniqueInstance === null) {
            self::$uniqueInstance = new Container;
        }

        return self::$uniqueInstance;
    }

    public function __clone()
    {
        throw new NotCloneableException('The container is not cloneable');
    }
}
