<?php
namespace Widmogrod\Functional;

use Widmogrod\FantasyLand\Monoid;
use Widmogrod\FantasyLand\Semigroup;

const mempty = 'Widmogrod\Functional\mempty';

/**
 * mempty :: a
 *
 * @param Monoid $a
 * @return Monoid
 */
function mempty(Monoid $a)
{
    return $a->getEmpty();
}

const mappend = 'Widmogrod\Functional\mappend';

/**
 * mappend :: a -> a -> a
 *
 * @param Semigroup $a
 * @param Semigroup $b
 * @return Semigroup
 */
function mappend(Semigroup $a, Semigroup $b)
{
    return $a->concat($b);
}
