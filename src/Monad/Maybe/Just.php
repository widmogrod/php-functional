<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Maybe;

use Widmogrod\Common;
use Widmogrod\FantasyLand;
use Widmogrod\Primitive\TypeMismatchError;

class Just implements Maybe
{
    use Common\PointedTrait;

    const of = 'Widmogrod\Monad\Maybe\Just::of';

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $applicative)
    {
        return $applicative->map($this->value);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation): FantasyLand\Functor
    {
        return self::of($this->bind($transformation));
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return $transformation($this->value);
    }

    /**
     * @inheritdoc
     */
    public function concat(FantasyLand\Semigroup $value): FantasyLand\Semigroup
    {
        if (!($value instanceof Maybe)) {
            throw new TypeMismatchError($value, Maybe::class);
        }

        if ($value instanceof Nothing) {
            return $this;
        }

        if (!($this->value instanceof FantasyLand\Semigroup)) {
            throw new TypeMismatchError($this->value, FantasyLand\Semigroup::class);
        }

        return self::of($this->value->concat($value->extract()));
    }

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return new Nothing();
    }

    /**
     * @inheritdoc
     */
    public function getEmpty()
    {
        return self::mempty();
    }

    /**
     * @inheritdoc
     */
    public function orElse(callable $fn)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return $this->value;
    }
}
