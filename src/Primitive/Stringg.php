<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

class Stringg implements
    FantasyLand\Pointed,
    FantasyLand\Monoid,
    FantasyLand\Setoid,
    Common\ValueOfInterface
{
    const of = 'Widmogrod\Primitive\Stringg::of';

    use Common\PointedTrait;
    use Common\ValueOfTrait;

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return self::of("");
    }

    /**
     * @inheritdoc
     */
    public function concat(FantasyLand\Semigroup $value): FantasyLand\Semigroup
    {
        if ($value instanceof self) {
            return self::of($this->value . $value->extract());
        }

        throw new TypeMismatchError($value, self::class);
    }

    /**
     * @inheritdoc
     */
    public function equals($other): bool
    {
        return $other instanceof self
            ? $this->value === $other->value
            : false;
    }
}
