<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

use FunctionalPHP\FantasyLand;
use Widmogrod\Common;

class Num implements
    FantasyLand\Pointed,
    FantasyLand\Setoid,
    Common\ValueOfInterface
{
    public const of = 'Widmogrod\Primitive\Num::of';

    use Common\PointedTrait;
    use Common\ValueOfTrait;

    /**
     * @inheritdoc
     */
    public function equals($other): bool
    {
        return $other instanceof self
            ? $this->extract() === $other->extract()
            : false;
    }
}
