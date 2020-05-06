<?php


namespace Delight\Box\Tests;

use Delight\Box\Container;
use Delight\Box\PersistentContainer;
use PHPUnit\Framework\TestCase;
use function Delight\Box\bind;
use function Delight\Box\box;
use function Delight\Box\resolve;
use function Delight\Box\bound;
use function Delight\Box\resolveClosure;
use function Delight\Box\resolveMethod;
use function Delight\Box\singleton;
use function Delight\Box\singletonBound;

class PersistentContainerTest extends TestCase
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

    public function test_functions_exists() {
        $this->assertTrue(function_exists('\Delight\Box\box'));
        $this->assertTrue(function_exists('\Delight\Box\bind'));
        $this->assertTrue(function_exists('\Delight\Box\bound'));
        $this->assertTrue(function_exists('\Delight\Box\singleton'));
        $this->assertTrue(function_exists('\Delight\Box\singletonBound'));
        $this->assertTrue(function_exists('\Delight\Box\resolve'));
        $this->assertTrue(function_exists('\Delight\Box\resolveMethod'));
        $this->assertTrue(function_exists('\Delight\Box\resolveClosure'));
    }

    public function test_functions_works() {
        $this->assertInstanceOf(Container::class, box());
        bind('some', 'value');
        $this->assertEquals('value', resolve('some'));
        $this->assertTrue(bound('some'));
        $this->assertFalse(bound('some_other_thing'));

        singleton('this', function () {
            return 'that';
        });
        $this->assertTrue(singletonBound('this'));
        $this->assertFalse(singletonBound('that'));

        $this->assertEquals('Hi!', resolveMethod(_DependencyInMethod::class, 'withResolvableDependency'));

        $this->assertEquals('hi', resolveClosure(function () {
            return 'hi';
        }));
    }
}
