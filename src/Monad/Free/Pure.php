<?php

namespace Widmogrod\Monad\Free;

use Widmogrod\Common;
use Widmogrod\FantasyLand\Apply;

class Pure implements MonadFree
{
    use Common\PointedTrait;

    const of = 'Widmogrod\Monad\Free\Pure::of';

    /**
     * @inheritdoc
     */
    public function ap(Apply $b)
    {
        return $b->map($this->value);
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $function)
    {
        return call_user_func($function, $this->value);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return self::of(call_user_func($function, $this->value));
    }

    /**
     * @inheritdoc
     */
    public function runFree(callable $interpretation)
    {
        return $this->value;
    }
}
