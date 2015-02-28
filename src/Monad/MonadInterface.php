<?php
namespace Monad;

interface MonadInterface extends BindInterface
{
    /**
     * Convert value to a monad.
     *
     * @param mixed $value
     * @return MonadInterface
     */
    public static function create($value);
}
