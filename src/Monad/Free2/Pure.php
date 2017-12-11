<?php

namespace Widmogrod\Monad\Free2;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Pure implements MonadFree
{
    use Common\PointedTrait;

    const of = 'Widmogrod\Monad\Free2\Pure::of';

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
     * ```
     * foldFree _ (Pure a)  = return a
     * ```
     *
     * @inheritdoc
     */
    public function foldFree(callable $f, callable $return): FantasyLand\Monad
    {
        return $return($this->value);
    }
}
