<?php
namespace Widmogrod\Primitive;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Stringg implements
    FantasyLand\Primitive,
    Common\ValueOfInterface
{
    const of = 'Widmogrod\Primitive\PString::of';

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
            return $value->map(function ($b) {
                return $this->value . $b;
            });
        }

        throw new TypeMismatchError($value, self::class);
    }

    /**
     * @param callable $function
     * @return Stringg
     */
    public function map(callable $function)
    {
        return self::of($function($this->value));
    }

    /**
     * @inheritdoc
     */
    public function equals($value)
    {
        return $value instanceof self
            ? $this->value === $value->extract()
            : false;
    }
}
