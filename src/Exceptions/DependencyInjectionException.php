<?php

namespace Delight\Box\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

class DependencyInjectionException extends Exception implements ContainerExceptionInterface
{
    /**
     * DependencyInjectionException constructor.
     * @param string $message
     * @param string ...$context
     */
    public function __construct(string $message, ...$context)
    {
        parent::__construct(
            sprintf($message, ...$context)
        );
    }
}
