<?php


namespace Delights\Box\Tests;

use Delights\Box\Container;
use Delights\Box\Exceptions\DependencyInjectionException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_it_can_resolve_binding_to_a_value()
    {
        $container = new Container();
        $container->bind('name', 'John Doe');
        $this->assertEquals('John Doe', $container->resolve('name'));

        $container->bind('name', 'Another name');
        $this->assertEquals('Another name', $container->resolve('name'));
    }

    public function test_it_can_mark_a_binding_as_bound()
    {
        $container = new Container();
        $container->bind('name', 'John Doe');
        $this->assertEquals('John Doe', $container->resolve('name'));
        $this->assertTrue($container->bound('name'));
    }

    public function test_it_can_instantiate_class_with_no_dependencies()
    {
        $container = new Container();
        $resolved = $container->resolve(_NoDependencies::class);

        $this->assertInstanceOf(_NoDependencies::class, $resolved);
        $this->assertEquals('Hi!', $resolved->sayHi());
    }

    public function test_it_can_instantiate_class_with_resolvable_dependencies()
    {
        $container = new Container();
        $resolved = $container->resolve(_ResolvableDependencies::class);

        $this->assertInstanceOf(_ResolvableDependencies::class, $resolved);
        $this->assertInstanceOf(_NoDependencies::class, $resolved->getNoDep());
    }

    public function test_it_can_resolve_default_parameters()
    {
        $container = new Container();
        $resolved = $container->resolve(_DependenciesWithDefaultParameters::class);

        $this->assertInstanceOf(_DependenciesWithDefaultParameters::class, $resolved);
        $this->assertEquals('John Doe', $resolved->getName());
    }

    public function test_it_can_resolve_nullable_parameters()
    {
        $container = new Container();
        $resolved = $container->resolve(_DependencyWithNullableParameter::class);

        $this->assertInstanceOf(_DependencyWithNullableParameter::class, $resolved);
        $this->assertNull($resolved->getName());
    }

    public function test_it_resolve_dependency_in_method()
    {
        $container = new Container();
        $resolved = $container->call(_DependencyInMethod::class, 'withResolvableDependency');

        $this->assertEquals('Hi!', $resolved);
    }

    public function test_it_resolve_dependency_in_method_using_resolve_by_calling_resolve_under_the_hood()
    {
        $container = $this->createPartialMock(Container::class, ['resolve']);

        $container->expects($this->exactly(2))
            ->method('resolve')
            ->willReturn(new _DependencyInMethod(), new _NoDependencies());

        $resolved = $container->call(_DependencyInMethod::class, 'withResolvableDependency');
        $this->assertEquals('Hi!', $resolved);
    }

    public function test_it_throws_an_exception_when_encountering_unresolvable_parameter()
    {
        $container = new Container();

        $this->expectException(DependencyInjectionException::class);
        $container->resolve(_UnresolvableParameters::class);
    }

    public function test_it_throws_an_exception_when_encountering_unexisting_class()
    {
        $container = new Container();

        $this->expectException(\InvalidArgumentException::class);
        $container->resolve(_NotExisting::class);
    }

    public function test_it_can_resolve_closures()
    {
        $container = new Container();

        $resolved = $container->closure(function (_NoDependencies $noDep, _ResolvableDependencies $resDep) {
            return 'Hello world!';
        });

        $this->assertEquals('Hello world!', $resolved);
    }

    public function test_it_can_bind_with_a_closure()
    {
        $container = new Container();

        $container->bind(_UnresolvableParameters::class, function (Container $container) {
            return new _UnresolvableParameters(15);
        });

        $this->assertInstanceOf(
            _UnresolvableParameters::class,
            $container->resolve(_UnresolvableParameters::class)
        );
    }

    public function test_it_can_bind_a_singleton()
    {
        $container = new Container();

        $container->singleton(_WithUniqueIDInConstructor::class, function () {
            return new _WithUniqueIDInConstructor();
        });

        $id = $container->resolve(_WithUniqueIDInConstructor::class);
        $id2 = $container->resolve(_WithUniqueIDInConstructor::class);

        $this->assertEquals($id, $id2);
    }

    public function test_it_can_resolve_arbitrary_parameters_works_in_constructor()
    {
        $container = new Container;

        $resolved = $container->resolve(_UnresolvableParameters::class, [
            'lamatitude' => 14
        ]);
        $this->assertInstanceOf(_UnresolvableParameters::class, $resolved);
        $this->assertEquals(14, $resolved->lamatitude);
    }

    public function test_it_can_resolve_arbitrary_parameters_works_in_closure()
    {
        $container = new Container;

        $resolver = $container->closure(function ($highest) {
            return $highest;
        }, ['highest' => 'yes']);
        $this->assertEquals('yes', $resolver);
    }

    public function test_it_can_resolve_arbitrary_parameters_works_in_method()
    {
        $container = new Container();

        $resolver = $container->call(_UnresolvableParametersInMethod::class, 'lama', [
            'lamatitude' => 1e4
        ]);

        $this->assertEquals(1e4, $resolver);
    }

    public function test_it_throws_an_error_when_methods_does_not_exists()
    {
        $container = new Container();

        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage('Method some does not exist');
        $container->call(_UnresolvableParameters::class, 'some');
    }

    public function test_it_can_bind_an_interface_to_an_implementation()
    {
        $container = new Container();

        $container->bindToImplementation(_SomeInterface::class, _ImplementingSomeInterface::class);
        $this->assertInstanceOf(_ImplementingSomeInterface::class, $container->resolve(_SomeInterface::class));
        $this->assertInstanceOf(
            _ImplementingSomeInterface::class,
            $container->resolve(_ImplementingSomeInterface::class)
        );

        $container->bindToImplementation(_SomeInterface::class, new _ImplementingSomeInterface());
        $this->assertInstanceOf(_ImplementingSomeInterface::class, $container->resolve(_SomeInterface::class));
        $this->assertInstanceOf(
            _ImplementingSomeInterface::class,
            $container->resolve(_ImplementingSomeInterface::class)
        );


        $container->bindToImplementation(_SomeInterface::class, function () {
            return new _ImplementingSomeInterface();
        });
        $this->assertInstanceOf(_ImplementingSomeInterface::class, $container->resolve(_SomeInterface::class));
        $this->assertInstanceOf(
            _ImplementingSomeInterface::class,
            $container->resolve(_ImplementingSomeInterface::class)
        );
    }
}

class _NoDependencies
{
    public function sayHi()
    {
        return 'Hi!';
    }
}

class _ResolvableDependencies
{
    /**
     * @var _NoDependencies
     */
    private _NoDependencies $noDep;

    public function __construct(_NoDependencies $noDep)
    {
        $this->noDep = $noDep;
    }

    public function getNoDep(): _NoDependencies
    {
        return $this->noDep;
    }
}

class _DependenciesWithDefaultParameters
{
    private string $name;

    public function __construct(string $name = 'John Doe')
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}

class _DependencyWithNullableParameter
{
    private ?string $name;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function __construct(?string $name)
    {
        $this->name = $name;
    }
}

class _DependencyInMethod
{
    public function withResolvableDependency(_NoDependencies $noDeps)
    {
        return 'Hi!';
    }
}

class _UnresolvableParameters
{
    public int $lamatitude;

    public function __construct(int $lamatitude)
    {
        $this->lamatitude = $lamatitude;
    }
}

class _WithUniqueIDInConstructor
{
    public $id;

    public function __construct()
    {
        $this->id = uniqid();
    }
}


class _UnresolvableParametersInMethod
{
    public int $lamatitude;

    public function lama(int $lamatitude)
    {
        return $lamatitude;
    }
}

interface _SomeInterface
{
}

class _ImplementingSomeInterface implements _SomeInterface
{
}
