<?php
namespace Monad\Maybe;

use Monad;
use Common;
use FantasyLand;

interface Maybe extends
    FantasyLand\Monad,
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
