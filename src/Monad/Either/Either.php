<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Either;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

interface Either extends
    FantasyLand\Monad,
    Common\ValueOfInterface
{
    /**
     * Depending on if is Left or is Right then it apply corresponding function
     *
     * @param callable $left  (a -> b)
     * @param callable $right (c -> b)
     *
     * @return mixed b
     */
    public function either(callable $left, callable $right);
}
