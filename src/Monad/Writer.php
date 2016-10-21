<?php
namespace Widmogrod\Monad;

use Widmogrod\Common;
use Widmogrod\FantasyLand;
use Widmogrod\Primitive\Stringg as S;

class Writer implements FantasyLand\Monad
{
    const of = 'Widmogrod\Monad\Writer::of';

    public static function of($value, FantasyLand\Monoid $side = null)
    {
        return new static($value, is_null($side) ? S::mempty() : $side);
    }

    /** @var mixed */
    protected $value;

    /** @var FantasyLand\Monoid $side */
    private $side;

    public function __construct($value, FantasyLand\Monoid $side)
    {
        $this->value = $value;
        $this->side = $side;
    }

    public function bind(callable $function)
    {
        list($value, $side) = call_user_func($function, $this->value)->runWriter();
        return new static($value, $this->side->concat($side));
    }

    public function ap(FantasyLand\Apply $b)
    {
        $new = $b->map($this->value);
        if($b instanceof Writer) {
            $new->side = $new->side->concat($b->side);
        }

        return $new;
    }

    public function map(callable $function)
    {
        return new static($function($this->value), $this->side);
    }

    public function runWriter()
    {
        return [$this->value, $this->side];
    }
}
