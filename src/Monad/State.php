<?php
namespace Widmogrod\Monad;

use Widmogrod\Common\PointedTrait;
use Widmogrod\FantasyLand\Apply;
use Widmogrod\FantasyLand\Monad;

class State implements Monad
{
    const of = 'Widmogrod\Monad\State::of';

    use PointedTrait;

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
    public function ap(Apply $b)
    {
        return $this->bind(function($f) use ($b) {
            return $b->map($f);
        });
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $function)
    {
        return self::of(function ($state) use ($function) {
            list($value, $newState) = $this->runState($state);
            return call_user_func($function, $value)->runState($newState);
        });
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return self::of(function ($state) use ($function) {
            list($value, $newState) = $this->runState($state);
            return [call_user_func($function, $value), $newState];
        });
    }

    /**
     * runState :: s -> (a, s)
     *
     * Run computation on a monad with initial state
     *
     * @param mixed $initialState
     * @return array
     */
    public function runState($initialState)
    {
        return call_user_func($this->value, $initialState);
    }
}
