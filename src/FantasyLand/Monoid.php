<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Monoid extends Semigroup
{
    /**
     * @return Monoid
     */
    public static function mempty();

    /**
     * Return identity element for given Semigroup
     *
     * @return Monoid
     */
    public function getEmpty();
}
