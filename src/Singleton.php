<?php


namespace Delight\Box;

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

    private function __clone()
    {
    }
}