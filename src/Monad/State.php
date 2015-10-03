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
            list($value, $newState) = $this->run($state);
            return call_user_func($function, $value)->run($newState);
        });
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return self::of(function ($state) use ($function) {
            list($value, $newState) = $this->run($state);
            return [call_user_func($function, $value), $newState];
        });
    }

    /**
     * @param mixed $state
     * @return mixed
     */
    public function run($state)
    {
        return call_user_func($this->value, $state);
    }
}