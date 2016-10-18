<?php
namespace Widmogrod\Functional;

use Widmogrod\FantasyLand\Monoid;

const mempty = 'Widmogrod\Functional\mempty';

function mempty(Monoid $a)
{
    return $a->getEmpty();
}

const mappend = 'Widmogrod\Functional\mappend';

function mappend(Monoid $a, Monoid $b)
{
    return $a->concat($b);
}
