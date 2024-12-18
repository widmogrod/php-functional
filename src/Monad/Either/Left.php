<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Either;

use FunctionalPHP\FantasyLand;
use Widmogrod\Common;

class Left implements Either
{
    use Common\PointedTrait;
    use Common\ValueOfTrait;

    public const of = 'Widmogrod\Monad\Either\Left::of';

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
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
        // Don't do anything
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function either(callable $left, callable $right)
    {
        return $left($this->value);
    }
}
