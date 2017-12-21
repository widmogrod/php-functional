<?php

namespace Widmogrod\Functional;

use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\ListtCons;
use Widmogrod\Primitive\ListtNil;
use function Widmogrod\Useful\match;

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
 * @param callable $fn
 * @param mixed $a
 * @return Listt
 */
function iterate(callable $fn, $a = null)
{
    return curryN(2, function (callable $fn, $a): Listt {
        return ListtCons::of(function () use ($fn, $a) {
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
 * @return mixed|ListtCons
 */
function repeat($a)
{
    return ListtCons::of(function () use ($a, &$list) {
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
 * @param int $n
 * @param mixed $a
 * @return Listt
 */
function replicate(int $n, $a = null): Listt
{
    return curryN(2, function (int $n, $a): Listt {
        if ($n < 1) {
            return fromNil();
        }

        return ListtCons::of(function () use ($n, $a) {
            return [$a, replicate($n - 1, $a)];
        });
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
 * @param Listt $l
 * @return Listt
 * @throws EmptyListError
 */
function cycle(Listt $l): Listt
{
    if ($l instanceof ListtNil) {
        throw new EmptyListError(__FUNCTION__);
    }

    $cycle = match([
        ListtNil::class => function () use (&$next) {
            return $next;
        },
        ListtCons::class => identity
    ]);

    $next = ListtCons::of(function () use ($l, $cycle) {
        return [head($l), $cycle(tail($l))];
    });

    return $next;
}
