<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Free;

use FunctionalPHP\FantasyLand;

interface MonadFree extends FantasyLand\Monad
{
    /**
     * ```
     * foldFree :: Monad m => (forall x . f x -> m x) -> Free f a -> m a
     * foldFree _ (Pure a)  = return a
     * foldFree f (Free as) = f as >>= foldFree f
     * ```
     *
     * @param callable $f      (f x -> m x)
     * @param callable $return
     *
     * @return FantasyLand\Monad
     */
    public function foldFree(callable $f, callable $return): FantasyLand\Monad;
}
