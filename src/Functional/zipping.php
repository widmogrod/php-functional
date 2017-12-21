<?php

namespace Widmogrod\Functional;

use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;

/**
 * @var callable
 */
const zip = 'Widmogrod\Functional\zip';

/**
 * zip :: [a] -> [b] -> [(a, b)]
 *
 * zip takes two lists and returns a list of corresponding pairs. If one input list is short, excess elements of the longer list are discarded.
 * zip is right-lazy:
 *
 * @param Listt $a
 * @param Listt|null $b
 * @return Listt
 */
function zip(Listt $a, Listt $b = null)
{
    return curryN(2, function (Listt $a, Listt $b): Listt {
        try {
            $x = head($a);
            $y = head($b);
            $xs = tail($a);
            $ys = tail($b);

            return prepend(
                [$x, $y],
                zip($xs, $ys)
            );
        } catch (EmptyListError $e) {
            return fromNil();
        }
    })(...func_get_args());
}

/**
 * @var callable
 */
const unzip = 'Widmogrod\Functional\unzip';

/**
 * unzip :: [(a, b)] -> ([a], [b])
 *
 * unzip transforms a list of pairs into a list of first components and a list of second components.
 *
 * @param Listt $a
 * @return array
 */
function unzip(Listt $a): array
{
    return foldr(function ($ab, $abs) {
        [$a, $b] = $ab;
        [$as, $bs] = $abs;

        return [
            prepend($a, $as),
            prepend($b, $bs)
        ];
    }, [fromNil(), fromNil()], $a);
}
