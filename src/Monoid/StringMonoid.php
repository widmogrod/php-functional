<?php

namespace Widmogrod\Monoid;

use Widmogrod\FantasyLand;

class StringMonoid implements FantasyLand\Monoid
{
    private $value;

    public function __construct($s) {
        $this->value = $s;
    }

    public function getEmpty() {
        return '';
    }

    public function get() {
        return $this->value;
    }

    public function concat(FantasyLand\Semigroup $value) {
        return new static($this->value . $value->get());
    }
}
