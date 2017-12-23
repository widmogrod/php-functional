<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Maybe;

use Widmogrod\FantasyLand;

class Nothing implements Maybe
{
    const of = 'Widmogrod\Monad\Maybe\Nothing::of';

    /**
     * @inheritdoc
     */
    public static function of($value)
    {
        return new static();
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $applicative): FantasyLand\Apply
    {
        return $this;
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
     */
    public function bind(callable $transformation)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function concat(FantasyLand\Semigroup $value): FantasyLand\Semigroup
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return new static();
    }

    /**
     * @inheritdoc
     */
    public function orElse(callable $fn)
    {
        return $fn();
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return null;
    }
}
