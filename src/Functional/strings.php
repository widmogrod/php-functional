<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

const concatStrings = 'Widmogrod\Functional\concatStrings';

/**
 * concatStrings :: String -> String -> String
 *
 * @param string      $a
 * @param string|null $b
 *
 * @return string|\Closure
 */
function concatStrings($a, $b = null)
{
    return curryN(2, function ($a, $b) {
        return $a . $b;
    })(...func_get_args());
}
