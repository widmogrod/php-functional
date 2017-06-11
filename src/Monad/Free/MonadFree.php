<?php

namespace Widmogrod\Monad\Free;

use Widmogrod\FantasyLand;

interface MonadFree extends
    FantasyLand\Monad
{
    public function runFree(callable $interpretation);
}
