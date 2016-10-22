<?php
namespace Widmogrod\Primitive;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Num implements
    FantasyLand\Pointed,
    FantasyLand\Setoid,
    Common\ValueOfInterface
{
    const of = 'Widmogrod\Primitive\Num::of';

    use Common\PointedTrait;
    use Common\ValueOfTrait;

    /**
     * @inheritdoc
     */
    public function equals($other)
    {
        return $other instanceof self
            ? $this->extract() === $other->extract()
            : false;
    }
}
