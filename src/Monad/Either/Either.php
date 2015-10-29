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
     * Depending on if is Left or is Right then it apply corresponding function and wrap it as a new monad
     *
     * @param callable $left
     * @param callable $right
     * @return Either
     */
    public function bimap(callable $left, callable $right);
}
