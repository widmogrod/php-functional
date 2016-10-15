<?php
namespace Widmogrod\Monad\Either;

use Widmogrod\Common\PointedTrait;
use Widmogrod\Common\ValueOfTrait;
use Widmogrod\FantasyLand;

class Right implements Either
{
    use PointedTrait;
    use ValueOfTrait;

    const of = 'Widmogrod\Monad\Either\Right::of';

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b)
    {
        return $b->map($this->value);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        return self::of($this->bind($transformation));
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }

    /**
     * @inheritdoc
     */
    public function either(callable $left, callable $right)
    {
        return call_user_func($right, $this->value);
    }
}
