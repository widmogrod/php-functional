<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Maybe;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

interface Maybe extends
    FantasyLand\Monad,
    Common\ValueOfInterface,
    FantasyLand\Monoid
{
    /**
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     *
     * @return Maybe
     */
    public function orElse(callable $fn);
}
