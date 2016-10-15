<?php
namespace Widmogrod\FantasyLand;

interface Monoid extends Semigroup
{
    /**
     * Return identity element for given Semigroup
     *
     * @return Monoid
     */
    public function getEmpty();
}
