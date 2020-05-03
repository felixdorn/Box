<?php

namespace Delight\Box\Exceptions;

use Exception;
use Throwable;

class DependencyInjectionException extends Exception
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
