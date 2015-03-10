<?php
namespace Functor;

class Nothing implements FunctorInterface
{
    public function map(callable $transformation)
    {
        return $this;
    }
}
