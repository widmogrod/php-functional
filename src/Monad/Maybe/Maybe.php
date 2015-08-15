<?php
namespace Monad\Maybe;

use Monad;
use Common;
use Functor;
use Applicative;

interface Maybe extends
    Monad\MonadInterface,
    Common\ValueOfInterface,
    Functor\FunctorInterface,
    Applicative\ApplicativeInterface
{
    /**
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     * @return Maybe
     */
    public function orElse(callable $fn);
}
