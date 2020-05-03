<?php

namespace Delight\Box;

use Closure;
use Delight\Box\Exceptions\DependencyInjectionException;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionParameter;

class Container
{
    /**
     * @var Container|null
     */
    private static ?Container $uniqueInstance = null;
    /**
     * @var mixed[]
     */
    private array $bindings = [];
    /**
     * @var mixed[]
     */
    private array $singletons = [];

    public static function getInstance(): Container
    {
        return self::$uniqueInstance ?? new self;
    }

    /**
     * @param string $id
     * @return mixed|object
     */
    public function resolve(string $id)
    {
        if ($this->singletonBound($id)) {
            return $this->singletons[$id];
        }

        if ($this->bound($id)) {
            $binding = $this->bindings[$id];

            if ($binding instanceof Closure) {
                return $this->resolveClosure($binding);
            }

            return $binding;
        }

        if (!class_exists($id)) {
            throw new InvalidArgumentException(
                sprintf('Can not autowire unexisting class [%s]', $id)
            );
        }

        return $this->resolveMethod($id, '__construct');
    }

    private function singletonBound(string $id): bool
    {
        return array_key_exists($id, $this->singletons);
    }

    /**
     * @param string $id
     * @return bool
     */
    public function bound(string $id): bool
    {
        return array_key_exists($id, $this->bindings);
    }

    /**
     * @param Closure $closure
     * @return mixed
     */
    public function resolveClosure(Closure $closure)
    {
        return call_user_func_array($closure, $this->makeMethodArguments(
            new ReflectionFunction($closure)
        ));
    }

    /**
     * @param ReflectionFunctionAbstract $function
     * @return mixed[]
     */
    private function makeMethodArguments(?ReflectionFunctionAbstract $function): array
    {
        if ($function === null) {
            return [];
        }

        return array_map(function (ReflectionParameter $parameter) use ($function) {
            if ($parameter->getClass() !== null) {
                return $this->resolve($parameter->getClass()->getName());
            }

            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
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
     * @param class-string $class
     * @param string $method
     * @return mixed|object
     */
    public function resolveMethod(string $class, string $method)
    {
        $ref = new ReflectionClass($class);

        if ($ref->hasMethod('__construct') === false && $method === '__construct') {
            return new $class;
        }

        if ($method === '__construct') {
            return $ref->newInstanceArgs(
                $this->makeMethodArguments($ref->getConstructor())
            );
        }

        return $ref->getMethod($method)->invokeArgs(
            $this->resolve($class),
            $this->makeMethodArguments($ref->getMethod($method))
        );
    }

    /**
     * @param string $id
     * @param mixed $value
     * @return $this
     */
    public function bind(string $id, $value): self
    {
        $this->bindings[$id] = $value;
        return $this;
    }

    public function singleton(string $class, Closure $resolver): self
    {
        $this->singletons[$class] = $this->resolveClosure($resolver);

        return $this;
    }
}
