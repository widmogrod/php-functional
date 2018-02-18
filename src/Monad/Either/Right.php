<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Either;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

class Right implements Either
{
    use Common\PointedTrait;
    use Common\ValueOfTrait;

    const of = 'Widmogrod\Monad\Either\Right::of';

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
    {
        return $b->map($this->value);
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
    public function either(callable $left, callable $right)
    {
        return $right($this->value);
    }
}
