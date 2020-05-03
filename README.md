<p align="center">
    <a href="https://github.com/delightphp/box">
        <img src="https://res.cloudinary.com/dy3jxhiba/image/upload/v1588493084/logo_rx8y5s.svg" width="150" alt="">
    </a>
    <h1 align="center">
        Box, a smart autowiring container for PHP.
    </h1>
    <p align="center">
        <img src="https://img.shields.io/packagist/l/delight/box" alt="License" />
        <img src="https://img.shields.io/packagist/v/delights/box" alt="Last Version" />
    </p>
</p>

## Getting started
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

## Security 
If you discover any security related issues, please email oss@dorns.fr instead of using the issue tracker.

## Credits
* [Félix Dorn](https://felixdorn.fr)


## Licensing
Copyright 2020 Félix Dorn

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.