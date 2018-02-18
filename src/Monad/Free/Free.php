<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Free;

use FunctionalPHP\FantasyLand;
use function Widmogrod\Functional\bind;

/**
 * Free (f (Free f a))
 *
 * Based on https://hackage.haskell.org/package/free-4.12.4/docs/Control-Monad-Free-Class.html
 */
class Free implements MonadFree
{
    const of = 'Widmogrod\Monad\Free\Free::of';

    /**
     * @var FantasyLand\Functor
     */
    private $f;

    public function __construct(FantasyLand\Functor $f)
    {
        $this->f = $f;
    }

    /**
     * @inheritdoc
     */
    public static function of($f)
    {
        return new self($f);
    }

    /**
     * ```
     * instance Functor f => Apply (Free f) where
     *   Pure a  <.> Pure b = Pure (a b)
     *   Pure a  <.> Free fb = Free $ fmap a <$> fb
     *   Free fa <.> b = Free $ (<.> b) <$> fa
     *
     * instance Functor f => Applicative (Free f) where
     *   pure = Pure
     *     Pure a <*> Pure b = Pure $ a b
     *     Pure a <*> Free mb = Free $ fmap a <$> mb
     *     Free ma <*> b = Free $ (<*> b) <$> ma
     *
     * ($) :: (a -> b) -> a -> b
     * (<*>) :: f (a -> b) -> f a -> f b
     * (<$>) :: Functor f => (a -> b) -> f a -> f b
     * ```
     *
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
    {
        return new self(
            $this->f->map(function ($ma) use ($b) {
                return $b->map($ma);
            })
        );
    }

    /**
     * ```
     * instance Functor f => Bind (Free f) where
     *   Pure a >>- f = f a
     *   Free m >>- f = Free ((>>- f) <$> m)
     * instance Functor f => Monad (Free f) where
     *   return = pure
     *     Pure a >>= f = f a
     *     Free m >>= f = Free ((>>= f) <$> m)
     *
     * (<$>) :: Functor f => (a -> b) -> f a -> f b
     * ```
     *
     * @inheritdoc
     */
    public function bind(callable $function)
    {
        return new self(
            $this->f->map(bind($function))
        );
    }

    /**
     * ```
     * instance Functor f => Functor (Free f) where
     *  fmap f = go where
     *      go (Pure a)  = Pure (f a)
     *      go (Free fa) = Free (go <$> fa)
     *
     * (<$>) :: Functor f => (a -> b) -> f a -> f b
     *```
     *
     * @inheritdoc
     */
    public function map(callable $go): FantasyLand\Functor
    {
        return new self(
            $this->f->map($go)
        );
    }

    /**
     * ```
     * foldFree f (Free as) = f as >>= foldFree f
     * ```
     *
     * @inheritdoc
     */
    public function foldFree(callable $f, callable $return): FantasyLand\Monad
    {
        return $f($this->f)->bind(function (MonadFree $next) use ($f, $return) : FantasyLand\Monad {
            return $next->foldFree($f, $return);
        });
    }
}
