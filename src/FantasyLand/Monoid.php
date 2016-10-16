<?php
namespace Widmogrod\FantasyLand;

interface Monoid extends Semigroup
{
    /**
     * Return identity element for given semigroup
     *
     * @return Monoid
     */
    public function getEmpty();
}
