<?php

namespace Widmogrod\Monad;

use Widmogrod\Common;
use Widmogrod\FantasyLand;
use Widmogrod\Monoid\StringMonoid;

class Writer implements FantasyLand\Monad
{
    const of = 'Widmogrod\Monad\Writer::of';

    public static function of($value, $side = '')
    {
        return new static($value, $side);
    }

    /** @var mixed */
    protected $value;

    /** @var FantasyLand\Monoid $side */
    private $side;

    public function __construct($value, $side = '')
    {
        $this->value = $value;

        if(is_string($side)) {
            $this->side = new StringMonoid($side);
        } else if($side instanceof FantasyLand\Monoid) {
            $this->side = $side;
        } else {
            throw new \RuntimeException("String or Monoid needed.");
        }
    }

    public function bind(callable $function)
    {
        $new = $function($this->value);
        $new->side = $this->side->concat($new->side);

        return new static($new->value, $new->side);
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
