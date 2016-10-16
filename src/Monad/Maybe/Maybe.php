<?php
namespace Widmogrod\Monad\Maybe;

use Widmogrod\Monad;
use Widmogrod\Common;
use Widmogrod\FantasyLand;

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
