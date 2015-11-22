<?php
namespace Monad\Either;

use Monad;
use Common;
use FantasyLand;

interface Either extends
    FantasyLand\Monad,
    Common\ValueOfInterface
{
    /**
     * Depending on if is Left or is Right then it apply corresponding function
     *
     * @param callable $left  (a -> b)
     * @param callable $right (c -> b)
     * @return mixed          b
     */
    public function either(callable $left, callable $right);
}
