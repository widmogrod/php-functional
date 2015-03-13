<?php
namespace Monad\Either;

use Common;
use Monad\Feature\LiftInterface;

class Left implements
    EitherInterface,
    LiftInterface
{
    use Common\CreateTrait;

    const create = 'Monad\Either\Left::create';

    /**
     * Converts values returned by regular function to monadic value.
     *
     * @param callable $transformation
     * @return LiftInterface
     */
    public function lift(callable $transformation)
    {
        return $this;
    }

    /**
     * Bind monad value to given $transformation function.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function bind(callable $transformation)
    {
        // Don't do anything
    }

    /**
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     * @return mixed
     */
    public function orElse(callable $fn)
    {
        return call_user_func($fn, $this->value);
    }
}
