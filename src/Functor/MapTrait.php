<?php
namespace Functor;

trait MapTrait 
{
    public function map(callable $transformation)
    {
        return static::create(call_user_func($transformation, $this->value));
    }
}
