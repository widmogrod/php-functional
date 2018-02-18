<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Free;

use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Monad;
use function Widmogrod\Functional\curryN;

/**
 * A version of lift that can be used with just a Functor for f.
 *
 * ```
 * liftF :: (Functor f, MonadFree f m) => f a -> m a
 * ```
 *
 * @param Functor $f
 *
 * @return MonadFree
 */
function liftF(Functor $f): MonadFree
{
    return Free::of($f);
}

/**
 * The very definition of a free monad is that given a natural transformation you get a monad homomorphism.
 *
 * ```
 * foldFree :: Monad m => (forall x . f x -> m x) -> Free f a -> m a
 * foldFree _ (Pure a)  = return a
 * foldFree f (Free as) = f as >>= foldFree f
 * ```
 *
 * @param callable  $interpreter (f x => m x)
 * @param MonadFree $free
 * @param callable  $return
 *
 * @return Monad|callable
 */
function foldFree(callable $interpreter, MonadFree $free = null, callable $return = null)
{
    return curryN(3, function (callable $interpreter, MonadFree $free, callable $return): Monad {
        return $free->foldFree($interpreter, $return);
    })(...func_get_args());
}
