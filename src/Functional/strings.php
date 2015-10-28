<?php
namespace Functional;

const concatStrings = 'Functional\concatStrings';

/**
 * concatStrings :: String -> String -> String
 *
 * @param string $a
 * @param string $b
 * @return string
 */
function concatStrings($a, $b = null)
{
    return call_user_func_array(curryN(2, function ($a, $b) {
        return $a . $b;
    }), func_get_args());
}
