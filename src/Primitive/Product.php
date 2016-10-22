<?php

namespace Widmogrod\Primitive;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Product extends Num implements
    FantasyLand\Monoid,
    FantasyLand\Pointed
{
    use Common\PointedTrait;

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return self::of(1);
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
    public function concat(FantasyLand\Semigroup $value)
    {
        if ($value instanceof self) {
            return self::of($this->extract() * $value->extract());
        }

        throw new TypeMismatchError($value, self::class);
    }
}
