<?php

namespace Delight\Box\Tests;

use Delight\Box\Container;
use PHPUnit\Framework\TestCase;

class Psr11Test extends TestCase
{
    public function test_get()
    {
        $container = new Container;
        $container->bind('some', 'value');
        $this->assertEquals('value', $container->get('some'));
    }

    public function test_has()
    {
        $container = new Container;
        $container->bind('some', 'value');
        $this->assertTrue($container->has('some'));
    }
}