<?php

namespace Delights\Box;

use Closure;
use Delights\Box\Exceptions\DependencyInjectionException;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;

class Container implements ContainerInterface
{
    use Psr11;

    /**
     * @var mixed[]
     */
    private array $bindings = [];
    /**
     * @var mixed[]
     */
    private array $singletons = [];

    /**
     * Resolve an id using Reflection
     * If a binding is found using the same id, then the actual binding will be returned
     * @param string $id
     * @param mixed[] $with
     * @return mixed|object
     */
    public function resolve(string $id, array $with = [])
    {
        if ($this->singletonBound($id)) {
            return $this->singletons[$id];
        }

        if ($this->bound($id)) {
            $binding = $this->bindings[$id];

            if ($binding instanceof Closure) {
                return $this->resolveClosure($binding, $with);
            }

            return $binding;
        }

        if (!class_exists($id)) {
            throw new InvalidArgumentException(
                sprintf('Can not autowire unexisting class [%s]', $id)
            );
        }

        return $this->resolveMethod($id, '__construct', $with);
    }

    /**
     * Check if a singleton is bound for an id
     * @param string $id
     * @return bool
     */
    public function singletonBound(string $id): bool
    {
        return array_key_exists($id, $this->singletons);
    }

    /**
     * Check if a binding exists for an id
     * @param string $id
     * @return bool
     */
    public function bound(string $id): bool
    {
        return array_key_exists($id, $this->bindings);
    }

    /**
     * Resolves a closure, execute it, and returns the result
     * @param Closure $closure
     * @param mixed[] $with
     * @return mixed
     */
    public function resolveClosure(Closure $closure, array $with = [])
    {
        return call_user_func_array($closure, $this->makeMethodArguments(
            new ReflectionFunction($closure),
            $with
        ));
    }

    /**
     * Takes a function reflection and returns an array of resolved parameters
     * @param ReflectionFunctionAbstract $function
     * @param mixed[] $with
     * @return mixed[]
     */
    private function makeMethodArguments(ReflectionFunctionAbstract $function, array $with = []): array
    {
        return array_map(function (ReflectionParameter $parameter) use ($function, $with) {
            if ($parameter->getClass() !== null) {
                return $this->resolve($parameter->getClass()->getName());
            }

            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            if (array_key_exists($parameter->getName(), $with)) {
                return $with[$parameter->getName()];
            }

            if ($parameter->allowsNull()) {
                return null;
            }

            throw new DependencyInjectionException(
                'Can not autowire parameter %s in %s',
                $parameter->getName(),
                $function->getName()
            );
        }, $function->getParameters());
    }

    /**
     * Resolve a method, execute it, and returns the result back
     * @param class-string $class
     * @param string $method
     * @param mixed[] $with
     * @return mixed|object
     */
    public function resolveMethod(string $class, string $method, array $with = [])
    {
        $ref = new ReflectionClass($class);

        if ($ref->hasMethod('__construct') === false && $method === '__construct') {
            return new $class;
        }

        if ($method === '__construct') {
            return $ref->newInstanceArgs(
                $this->makeMethodArguments($ref->getConstructor(), $with),
            );
        }

        return $ref->getMethod($method)->invokeArgs(
            $this->resolve($class),
            $this->makeMethodArguments($ref->getMethod($method), $with)
        );
    }

    /**
     * Bind an id to a value
     * @param string $id
     * @param mixed $value
     * @return $this
     */
    public function bind(string $id, $value): self
    {
        $this->bindings[$id] = $value;
        return $this;
    }

    /**
     * Bind a singleton to an id
     * @param string $class
     * @param Closure $resolver
     * @return $this
     */
    public function singleton(string $class, Closure $resolver): self
    {
        $this->singletons[$class] = $this->resolveClosure($resolver);

        return $this;
    }

    /**
     * Binds an interface to an implementation
     * If the implementation is a
     * For a given Interface and a given Implementation
     * We bind Interface -> Implementation
     * And Implementation::class -> Implementation
     * @param string $interface
     * @param string|object|Closure $implementation
     * @return $this
     */
    public function bindToImplementation(string $interface, $implementation): Container
    {
        if (is_string($implementation)) {
            $implementation = $this->resolve($implementation);
        }

        if ($implementation instanceof Closure) {
            $implementation = $this->resolveClosure($implementation);
        }

        $this->bind($interface, $implementation);
        $this->bind(get_class($implementation), $implementation);

        return $this;
    }
}
