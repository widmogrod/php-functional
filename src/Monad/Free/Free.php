<?php

namespace Widmogrod\Monad\Free;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Free implements MonadFree
{
    use Common\PointedTrait;

    const of = 'Widmogrod\Monad\Free\Free::of';

    private $fn;

    /**
     * @inheritdoc
     */
    public static function of($functor, $value = null)
    {
        return new self($functor, $value);
    }

    private function __construct(callable $fn, $value = null)
    {
        $this->fn = $fn;
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $b)
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
        return self::of(function ($x) use ($function) {
            return call_user_func($this->fn, $x)->bind($function);
        }, $this->value);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return self::of(function ($x) use ($function) {
            return call_user_func($this->fn, $x)->map($function);
        }, $this->value);
    }

    public function runFree(callable $interpretation)
    {
        return $interpretation($this->value)->bind(function ($result) use ($interpretation) {
            return call_user_func($this->fn, $result)->runFree($interpretation);
        });
    }
}
