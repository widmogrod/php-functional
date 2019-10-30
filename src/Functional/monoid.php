<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

use FunctionalPHP\FantasyLand\Monoid;
use FunctionalPHP\FantasyLand\Semigroup;

const emptyM = 'Widmogrod\Functional\emptyM';

/**
 * emptyM :: a
 *
 * @param Monoid $a
 *
 * @return Monoid
 */
function emptyM(Monoid $a): Monoid
{
    return $a::mempty();
}

const concatM = 'Widmogrod\Functional\concatM';

/**
 * concatM :: a -> a -> a
 *
 * @param Semigroup      $a
 * @param Semigroup|null $b
 *
 * @return Semigroup|\Closure
 */
function concatM(Semigroup $a, Semigroup $b = null)
{
    return curryN(2, function (Semigroup $a, Semigroup $b) {
        return $a->concat($b);
    })(...func_get_args());
}
