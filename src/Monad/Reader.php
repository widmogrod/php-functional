<?php

namespace Widmogrod\Monad;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Reader implements FantasyLand\Monad
{
    const of = 'Widmogrod\Monad\Reader::of';

    use Common\PointedTrait;

    /**
     * @param callable $continuation
     */
    public function __construct(callable $continuation)
    {
        $this->value = $continuation;
    }

    public function bind(callable $function)
    {
        return self::of(function ($env) use ($function) {
            return call_user_func($function, $this->runReader($env))->runReader($env);
        });
    }

    public function ap(FantasyLand\Apply $b)
    {
        return $this->bind(function ($f) use ($b) {
            return $b->map($f);
        });
    }

    public function map(callable $function)
    {
        return self::of(function ($env) use ($function) {
            return call_user_func($function, $this->runReader($env));
        });
    }

    public function runReader($env)
    {
        return call_user_func($this->value, $env);
    }
}
