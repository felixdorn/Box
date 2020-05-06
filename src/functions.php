<?php

namespace Delight\Box;


use Closure;

function box(): Container
{
    return PersistentContainer::getInstance();
}

/**
 * @param string $id
 * @param array $with
 * @return mixed
 */
function resolve(string $id, array $with = [])
{
    return PersistentContainer::resolve($id, $with);
}

/**
 * @param string $id
 * @param mixed $value
 * @return Container
 */
function bind(string $id, $value): Container {
    return PersistentContainer::bind($id, $value);
}

/**
 * @param string $id
 * @param Closure $closure
 * @return Container
 */
function singleton(string $id, Closure $closure): Container {
    return PersistentContainer::singleton($id, $closure);
}

/**
 * @param Closure $closure
 * @param array $with
 * @return mixed
 */
function resolveClosure(Closure $closure, array $with = [])
{
    return PersistentContainer::resolveClosure($closure, $with);
}

/**
 * @param string $class
 * @param string $method
 * @param array $with
 * @return mixed
 */
function resolveMethod(string $class, string $method, array $with = []) {
    return PersistentContainer::resolveMethod($class, $method, $with);
}

/**
 * @param string $id
 * @return bool
 */
function bound(string $id): bool {
    return PersistentContainer::bound($id);
}

/**
 * @param string $id
 * @return bool
 */
function singletonBound(string $id): bool {
    return PersistentContainer::singletonBound($id);
}
