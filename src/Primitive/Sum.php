<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

use FunctionalPHP\FantasyLand;

class Sum extends Num implements
    FantasyLand\Monoid,
    FantasyLand\Pointed
{
    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return self::of(0);
    }

    /**
     * @inheritdoc
     */
    public function concat(FantasyLand\Semigroup $value): FantasyLand\Semigroup
    {
        if ($value instanceof self) {
            return self::of($this->extract() + $value->extract());
        }

        throw new TypeMismatchError($value, self::class);
    }
}
