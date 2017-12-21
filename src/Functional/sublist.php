<?php

namespace Widmogrod\Functional;

use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;

/**
 * @var callable
 */
const take = 'Widmogrod\Functional\take';

/**
 * take :: Int -> [a] -> [a]
 *
 * take n, applied to a list xs, returns the prefix of xs of length n, or xs itself if n > length xs:
 *
 * @param int $n
 * @param Listt $xs
 * @return Listt
 */
function take(int $n, Listt $xs = null)
{
    return curryN(2, function (int $n, Listt $xs): Listt {
        if ($n < 1) {
            return fromNil();
        }

        return $xs::of(function () use ($n, $xs) {
            return [head($xs), take($n - 1, tail($xs))];
        });
    })(...func_get_args());
}

/**
 * @var callable
 */
const drop = 'Widmogrod\Functional\drop';

/**
 * drop :: Int -> [a] -> [a]
 *
 * drop n xs returns the suffix of xs after the first n elements, or [] if n > length xs:
 * @param int $n
 * @param Listt $xs
 * @return Listt
 */
function drop(int $n, Listt $xs = null)
{
    return curryN(2, function (int $n, Listt $xs): Listt {
        if ($n < 1) {
            return $xs;
        }

        try {
            return drop($n - 1, tail($xs));
        } catch (EmptyListError $e) {
            return fromNil();
        }
    })(...func_get_args());
}
