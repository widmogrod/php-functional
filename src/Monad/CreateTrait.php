<?php
namespace Monad;

trait CreateTrait
{
    /**
     * Convert value to monad
     *
     * @param mixed $value
     * @return MonadInterface|LiftInterface
     */
    public static function create($value)
    {
        return $value instanceof self
            ? $value
            : new static($value);
    }
}