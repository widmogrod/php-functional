<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

use FunctionalPHP\FantasyLand;
use Widmogrod\Common;

class ListtNil implements Listt, \IteratorAggregate
{
    use Common\PointedTrait;

    public const of = 'Widmogrod\Primitive\ListtConst::of';

    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation): FantasyLand\Functor
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * fs <*> xs = [f x | f <- fs, x <- xs]
     */
    public function ap(FantasyLand\Apply $applicative): FantasyLand\Apply
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $function, $accumulator)
    {
        return $accumulator;
    }

    /**
     * @inheritdoc
     *
     * Example from haskell source code:
     * ``` haskell
     * traverse f = List.foldr cons_f (pure [])
     *  where cons_f x ys = (:) <$> f x <*> ys
     * ```
     *
     * (<$>) :: Functor f => (a -> b) -> f a -> f b
     * (<*>) :: f (a -> b) -> f a -> f b
     */
    public function traverse(callable $fn)
    {
        throw new EmptyListError(__FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return self::of([]);
    }

    /**
     * @inheritdoc
     *
     * @throws TypeMismatchError
     */
    public function concat(FantasyLand\Semigroup $value): FantasyLand\Semigroup
    {
        if ($value instanceof Listt) {
            return $value;
        }

        throw new TypeMismatchError($value, self::class);
    }

    /**
     * @inheritdoc
     */
    public function equals($other): bool
    {
        return $other instanceof self
            ? true
            : false;
    }

    /**
     * @inheritdoc
     */
    public function head()
    {
        throw new EmptyListError(__FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function tail(): Listt
    {
        throw new EmptyListError(__FUNCTION__);
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new \ArrayObject();
    }
}
