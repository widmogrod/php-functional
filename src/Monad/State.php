<?php
namespace Monad;

use Common;
use FantasyLand\ApplyInterface;
use FantasyLand\MonadInterface;

class State implements MonadInterface
{
    const of = 'Monad\State::of';

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
    public function ap(ApplyInterface $b)
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
     * runState :: s
     *
     * Run computation on a monad with initial state
     *
     * @param mixed $state
     * @return Array
     */
    public function runState($state)
    {
        return call_user_func($this->value, $state);
    }
}
