<?php
namespace Monad;

interface MonadInterface
{
    /**
     * Bind monad value to given $transformation function.
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation);

    /**
     * Convert value to a monad.
     *
     * @param mixed $value
     * @return MonadInterface
     */
    public static function create($value);
}
