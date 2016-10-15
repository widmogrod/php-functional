<?php
namespace Widmogrod\FantasyLand;

interface Semigroup
{
    /**
     * Return result of applying one semigroup with another.
     *
     * @param Semigroup $value
     * @return Semigroup
     */
    public function concat(Semigroup $value);
}
