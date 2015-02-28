<?php
namespace Monad;

interface LiftInterface extends MonadInterface
{
    /**
     * Converts values returned by regular function to monadic value.
     *
     * @param callable $transformation
     * @return LiftInterface
     */
    public function lift(callable $transformation);
}