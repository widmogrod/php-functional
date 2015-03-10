<?php
namespace Functor;

use Common;

class Maybe implements
    FunctorInterface,
    Common\CreateInterface
{
    use MapTrait;

    /**
     * Convert value to a new context.
     *
     * @param mixed $value
     * @return self
     */
    public static function create($value)
    {
        if (null === $value) {
            return new Nothing();
        } else {
            return new self($value);
        }
    }
}
