<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Maybe;

use FunctionalPHP\FantasyLand;
use Widmogrod\Common;

interface Maybe extends
    FantasyLand\Monad,
    FantasyLand\Foldable,
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
