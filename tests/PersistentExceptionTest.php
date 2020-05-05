<?php


namespace Delight\Box\Tests;


use Delight\Box\Container;
use Delight\Box\Exceptions\NotCloneableException;
use Delight\Box\PersistentContainer;
use PHPUnit\Framework\Error\Error;
use PHPUnit\Framework\TestCase;

class PersistentExceptionTest extends TestCase
{
    public function test_singleton_returns_an_instance_of_the_container()
    {
        $this->assertInstanceOf(Container::class, PersistentContainer::getInstance());
    }

    public function test_singleton_works_as_expected()
    {
        PersistentContainer::getInstance()->bind('some', 'thing');

        $this->assertEquals('thing', PersistentContainer::getInstance()->resolve('some'));
    }

    public function test_container_methods_are_available_statically_in_the_singleton()
    {
        PersistentContainer::bind('some', 'thing');
        $this->assertEquals('thing', PersistentContainer::resolve('some'));

        $this->assertTrue(PersistentContainer::bound('some'));

        $result = PersistentContainer::resolveClosure(function ($that) {
            return $that;
        }, ['that' => 4]);
        $this->assertEquals(4, $result);

        $this->assertInstanceOf(
            _ResolvableDependencies::class,
            PersistentContainer::resolve(_ResolvableDependencies::class)
        );

        $this->assertEquals(
            12,
            PersistentContainer::resolveMethod(_UnresolvableParametersInMethod::class, 'lama', ['lamatitude' => 12])
        );
    }

    public function test_it_can_clear_the_container()
    {
        PersistentContainer::bind('some', 'value');
        $this->assertTrue(PersistentContainer::bound('some'));
        PersistentContainer::clear();
        $this->assertFalse(PersistentContainer::bound('some'));
    }
}