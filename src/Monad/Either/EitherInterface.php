<?php
namespace Monad\Either;

use Monad\MonadInterface;

interface EitherInterface extends MonadInterface
{
    /**
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     * @return mixed
     */
    public function orElse(callable $fn);
}