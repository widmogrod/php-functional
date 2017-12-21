<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Chain extends Apply
{
    /**
     * bind :: Monad m => (a -> m b) -> m b
     *
     * @return mixed|\Closure
     *
     * @param callable $function
     *
     * @return self
     */
    public function bind(callable $function);
}
