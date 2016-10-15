<?php
namespace Widmogrod\Monad\Either;

use Widmogrod\Common\PointedTrait;
use Widmogrod\Common\ValueOfTrait;
use Widmogrod\FantasyLand;

class Left implements Either
{
    use PointedTrait;
    use ValueOfTrait;

    const of = 'Widmogrod\Monad\Either\Left::of';

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
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
        return call_user_func($left, $this->value);
    }
}
