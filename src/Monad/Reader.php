<?php

declare(strict_types=1);

namespace Widmogrod\Monad;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

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
            return $function($this->runReader($env))->runReader($env);
        });
    }

    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
    {
        return $this->bind(function ($f) use ($b) {
            return $b->map($f);
        });
    }

    public function map(callable $function): FantasyLand\Functor
    {
        return self::of(function ($env) use ($function) {
            return $function($this->runReader($env));
        });
    }

    public function runReader($env)
    {
        return call_user_func($this->value, $env);
    }
}
