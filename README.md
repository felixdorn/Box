<p align="center">
    <a href="https://github.com/felixdorn/box">
        <img src="https://res.cloudinary.com/dy3jxhiba/image/upload/v1588493084/logo_rx8y5s.svg" width="150" alt="">
    </a>
    <h1 align="center">
        Box, a smart autowiring container for PHP.
    </h1>
    <p align="center">
        <img src="https://github.com/felixdorn/box/workflows/CI/badge.svg?branch=master" alt="CI" />
        <img src="https://github.styleci.io/repos/260858314/shield?branch=master&style=flat" alt="Style CI" />
        <img alt="Codecov" src="https://img.shields.io/codecov/c/github/felixdorn/box">
        <img src="https://img.shields.io/packagist/l/delights/box" alt="License" />
        <img src="https://img.shields.io/packagist/v/delights/box" alt="Last Version" />
    </p>
</p>

## Getting started

### Installation
This library can be installed using composer, if you don't have it already, [download it](https://getcomposer.org/download).

You can either run this command :
```bash
composer require delights/box
```
Or by adding a requirement in your `composer.json` :
```json
{
  "require": {
    "delights/box": "1.0.1"  
  }
}
```
Don't forget to run `composer install` after adding the requirement.

Before diving into the autowiring and stuff, we need to create a container instance.
```php
use Delight\Box\Container;

$container = new Container();
```

## Bindings
You can bind something into the container, it works just like a key => value array.
```php
$container->bind('key', 'value');
echo $container->resolve('key'); // prints "value"
```

You may want to bind a class in a closure if you need more specific arguments,
```php
$container->bind(Crawler::class, function () {
    return new Crawler('f4dg65gd6fg465g');
});
```
Every time you ask for a `Crawler::class`, this closure will be executed and a fresh instance of `Crawler` will be created.
To avoid that, you can use the `singleton` method.

```php
$container->singleton(Connection::class, function () {
    return new Connection();
});
```
Now, the `Connection` class will be instantiated once, and the closure never executed again. 

## Resolving
Smartly resolving parameters is the primary goal of this package. You can resolve anything that needs parameter including, constructors, closures, methods, functions.
We even support resolving properties if there is an annotation .

### Autowiring
Autowiring allows the container to auto-magically resolve dependencies using the Reflection API.

```php
use Delight\Box\Container;
$container = new Container();

class SomeClass {}
$container->resolve(SomeClass::class); // returns an instance of "SomeClass" 

class SomeOtherClass {
    public function __construct(SomeClass $dep) {
        $this->dep = $dep;
    }
}
$container->resolve(SomeOtherClass::class); 
// returns an instance of "SomeOtherClass"
// with $this->dep set to an instance of "SomeClass"
```

The container can resolve non-typed argument but only in two cases : they should either allow null or have a default value.

### Resolving an object method
```php
class PostsRepository {
    public function all() {
        return ['My article'];    
    }
}

class PostController {
    public function index(PostsRepository $repository) {
        return $repository->all();    
    }
}

$container->resolveMethod(PostController::class, 'index');
// This returns |'My article']
```
### Resolving a Closure
```php
$container->resolveClosure(function (SomeClass $class) {
    return $class instanceof SomeClass;
}); // returns true
```

### Resolve with arbitrary parameters
```php
class Vec2 {
    protected int $x;
    protected int $y;

    public function __construct(int $x, int $y) {
        $this->x = $x;
        $this->y = $y;
    }
}
$container->resolve(Vec2::class, [
    'x' => 2,
    'y' => 4
]); // returns an instance of "Vec2" 
```

The order does not matter, in our example, it can be either `[x, y]` or `[y, x]`.

You can pass arbitrary parameters to any resolve function.

## Security 
If you discover any security related issues, please email oss@dorns.fr instead of using the issue tracker.

## Credits
* [Félix Dorn](https://felixdorn.fr)

## Licensing
Copyright 2020 Félix Dorn

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
