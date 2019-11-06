<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\ListtCons;

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
 * @param  Listt          $xs
 * @param  Listt|null     $ys
 * @return Listt|\Closure
 */
function zip(Listt $xs, Listt $ys = null)
{
    return curryN(2, function (Listt $xs, Listt $ys): Listt {
        try {
            $x = head($xs);
            $y = head($ys);

            return new ListtCons(function () use ($x, $y, $xs, $ys) {
                return [
                    [$x, $y],
                    zip(tail($xs), tail($ys))
                ];
            });
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
 * @param  Listt $xs
 * @return array
 */
function unzip(Listt $xs): array
{
    try {
        [$x, $y] = head($xs);

        return [
            new ListtCons(function () use ($x, $xs) {
                return [$x, unzip(tail($xs))[0]];
            }),
            new ListtCons(function () use ($y, $xs) {
                return [$y, unzip(tail($xs))[1]];
            }),
        ];
    } catch (EmptyListError $e) {
        return [fromNil(), fromNil()];
    }
}
