<?php
namespace Monad\Maybe;

use Monad;
use Common;
use Functor;
use FantasyLand;
use Applicative;

interface Maybe extends
    FantasyLand\MonadInterface,
    Common\ValueOfInterface
{
    /**
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     * @return Maybe
     */
    public function orElse(callable $fn);
}
