<?php

declare(strict_types=1);

namespace Widmogrod\Monad;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

class State implements FantasyLand\Monad
{
    const of = 'Widmogrod\Monad\State::of';

    use Common\PointedTrait;

    /**
     * @param callable $continuation
     */
    public function __construct(callable $continuation)
    {
        $this->value = $continuation;
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
    {
        return $this->bind(function ($f) use ($b) {
            return $b->map($f);
        });
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $function)
    {
        return self::of(function ($state) use ($function) {
            [$value, $newState] = $this->runState($state);
            $m = $function($value);

            return $m instanceof State
                ? $m->runState($newState)
                : [$m, $newState];
        });
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): FantasyLand\Functor
    {
        return self::of(function ($state) use ($function) {
            [$value, $newState] = $this->runState($state);

            return [$function($value), $newState];
        });
    }

    /**
     * runState :: s -> (a, s)
     *
     * Run computation on a monad with initial state
     *
     * @param mixed $initialState
     *
     * @return array
     */
    public function runState($initialState)
    {
        return call_user_func($this->value, $initialState);
    }
}
