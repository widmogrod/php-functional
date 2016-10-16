<?php
namespace Widmogrod\Monad\Either;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Right implements Either
{
    use Common\PointedTrait;
    use Common\ValueOfTrait;

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
