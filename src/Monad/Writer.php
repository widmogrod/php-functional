<?php

declare(strict_types=1);

namespace Widmogrod\Monad;

use FunctionalPHP\FantasyLand;
use Widmogrod\Primitive\Stringg as S;

class Writer implements FantasyLand\Monad
{
    const of = 'Widmogrod\Monad\Writer::of';

    public static function of($value, FantasyLand\Monoid $side = null)
    {
        return new static($value, $side === null ? S::mempty() : $side);
    }

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var FantasyLand\Monoid $side
     */
    private $side;

    public function __construct($value, FantasyLand\Monoid $side)
    {
        $this->value = $value;
        $this->side = $side;
    }

    public function bind(callable $function)
    {
        [$value, $side] = $function($this->value)->runWriter();

        return new static($value, $this->side->concat($side));
    }

    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
    {
        return $this->bind(function ($f) use ($b) {
            return $b->map($f);
        });
    }

    public function map(callable $function): FantasyLand\Functor
    {
        return static::of($function($this->value), $this->side);
    }

    public function runWriter()
    {
        return [$this->value, $this->side];
    }
}
