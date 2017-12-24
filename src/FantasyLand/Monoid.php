<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Monoid extends Semigroup
{
    /**
     * @return Monoid
     */
    public static function mempty();
}
