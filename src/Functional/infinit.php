<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\ListtCons;
use Widmogrod\Primitive\ListtNil;

/**
 * @var callable
 */
const iterate = 'Widmogrod\Functional\iterate';

/**
 * iterate :: (a -> a) -> a -> [a]
 *
 * iterate f x returns an infinite list of repeated applications of f to x:
 *
 * ```haskell
 * iterate f x == [x, f x, f (f x), ...]
 * ```
 * @param  callable       $fn
 * @param  mixed          $a
 * @return Listt|\Closure
 */
function iterate(callable $fn, $a = null)
{
    return curryN(2, function (callable $fn, $a): Listt {
        return new ListtCons(function () use ($fn, $a) {
            return [$a, iterate($fn, $fn($a))];
        });
    })(...func_get_args());
}

/**
 * @var callable
 */
const repeat = 'Widmogrod\Functional\repeat';

/**
 * repeat :: a -> [a]
 *
 * repeat x is an infinite list, with x the value of every element.
 *
 * @param $a
 * @return ListtCons
 */
function repeat($a)
{
    return new ListtCons(function () use ($a, &$list) {
        return [$a, repeat($a)];
    });
}

/**
 * @var callable
 */
const replicate = 'Widmogrod\Functional\replicate';

/**
 * replicate :: Int -> a -> [a]
 *
 * replicate n x is a list of length n with x the value of every element.
 * It is an instance of the more general genericReplicate, in which n may be of any integral type.
 *
 * @param  int            $n
 * @param  mixed          $a
 * @return Listt|\Closure
 */
function replicate(int $n, $a = null): Listt
{
    return curryN(2, function (int $n, $a): Listt {
        return take($n, repeat($a));
    })(...func_get_args());
}

/**
 * @var callable
 */
const cycle = 'Widmogrod\Functional\cycle';

/**
 * cycle :: [a] -> [a]
 *
 * cycle ties a finite list into a circular one, or equivalently, the infinite repetition of the original list. It is the identity on infinite lists.
 *
 * @param  Listt          $xs
 * @return Listt
 * @throws EmptyListError
 */
function cycle(Listt $xs): Listt
{
    if ($xs instanceof ListtNil) {
        throw new EmptyListError(__FUNCTION__);
    }

    $cycle = function (Listt $xs, Listt $cycled) use (&$cycle) : Listt {
        if ($cycled instanceof ListtNil) {
            return cycle($xs);
        }

        return new ListtCons(function () use ($xs, $cycled, $cycle) {
            return [head($cycled), $cycle($xs, tail($cycled))];
        });
    };

    return $cycle($xs, $xs);
}
