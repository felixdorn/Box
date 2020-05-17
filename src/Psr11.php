<?php


namespace Delights\Box;

trait Psr11
{
    public function get($id)
    {
        return $this->resolve($id);
    }

    public function has($id)
    {
        return $this->bound($id) || $this->singletonBound($id);
    }
}
