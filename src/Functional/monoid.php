<?php

namespace Widmogrod\Functional;

use Widmogrod\FantasyLand\Monoid;
use Widmogrod\FantasyLand\Semigroup;

const emptyM = 'Widmogrod\Functional\emptyM';

/**
 * emptyM :: a
 *
 * @param Monoid $a
 *
 * @return Monoid
 */
function emptyM(Monoid $a)
{
    return $a->getEmpty();
}

const concatM = 'Widmogrod\Functional\concatM';

/**
 * concatM :: a -> a -> a
 *
 * @param Semigroup $a
 * @param Semigroup $b
 *
 * @return Semigroup
 */
function concatM(Semigroup $a, Semigroup $b)
{
    return curryN(2, function (Semigroup $a, Semigroup $b) {
        return $a->concat($b);
    })(...func_get_args());
}
