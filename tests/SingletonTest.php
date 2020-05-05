<?php

namespace Delight\Box\Tests;


use Delight\Box\Container;
use Delight\Box\Exceptions\NotCloneableException;
use Delight\Box\Singleton;
use PHPUnit\Framework\TestCase;

class SingletonTest extends TestCase
{
    public function test_singleton_returns_an_instance_of_the_container()
    {
        $this->assertInstanceOf(Container::class, Singleton::getInstance());
    }

    public function test_cloning_throws_an_error()
    {
        $this->expectException(NotCloneableException::class);
        $this->expectWarningMessage("The container is not cloneable");
        $container = Container::getInstance();
        clone $container;
    }

    public function test_singleton_works_as_expected()
    {
        Container::getInstance()->bind('some', 'thing');

        $this->assertEquals('thing', Container::getInstance()->resolve('some'));
    }
}