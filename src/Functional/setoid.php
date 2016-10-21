<?php
namespace Widmogrod\Functional;

use Widmogrod\FantasyLand\Setoid;

const equal = 'Widmogrod\Functional\equal';

/**
 * equal :: a -> a -> Bool
 *
 * @param Setoid $a
 * @param Setoid $b
 * @return boolean
 */
function equal(Setoid $a, Setoid $b = null)
{
    return call_user_func_array(curryN(2, function (Setoid $a, Setoid $b) {
        return $a->equals($b);
    }), func_get_args());

}
