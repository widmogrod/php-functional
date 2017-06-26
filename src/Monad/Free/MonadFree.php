<?php

namespace Widmogrod\Monad\Free;

use Widmogrod\FantasyLand;

interface MonadFree extends
    FantasyLand\Monad
{
    /**
     * Run interpretation
     *
     * @param callable $interpretation
     *
     * @return mixed
     */
    public function runFree(callable $interpretation);
}
