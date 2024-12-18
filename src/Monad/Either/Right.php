<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Either;

use FunctionalPHP\FantasyLand;
use Widmogrod\Common;

class Right implements Either
{
    use Common\PointedTrait;
    use Common\ValueOfTrait;

    public const of = 'Widmogrod\Monad\Either\Right::of';

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
