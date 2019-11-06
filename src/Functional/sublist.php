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
const take = 'Widmogrod\Functional\take';

/**
 * take :: Int -> [a] -> [a]
 *
 * take n, applied to a list xs, returns the prefix of xs of length n, or xs itself if n > length xs:
 *
 * @param  int            $n
 * @param  Listt          $xs
 * @return Listt|\Closure
 */
function take(int $n, Listt $xs = null)
{
    return curryN(2, function (int $n, Listt $xs): Listt {
        if ($n < 1) {
            return fromNil();
        }

        return new $xs(function () use ($n, $xs) {
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
 * @param  int            $n
 * @param  Listt          $xs
 * @return Listt|\Closure
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

/**
 * @var callable
 */
const dropWhile = 'Widmogrod\Functional\dropWhile';

/**
 *
 * dropWhile :: (a -> Bool) -> [a] -> [a]
 *
 * ```haskell
 * dropWhile _ []          =  []
 * dropWhile p xs@(x:xs')
 *  | p x       =  dropWhile p xs'
 *  | otherwise =  xs
 * ```
 *
 * @param  callable       $predicate
 * @param  Listt          $xs
 * @return Listt|\Closure
 */
function dropWhile(callable $predicate, Listt $xs = null)
{
    return curryN(2, function (callable $predicate, Listt $xs): Listt {
        if ($xs instanceof ListtNil) {
            return $xs;
        }

        $tail = $xs;
        do {
            $x = head($tail);
            if (!$predicate($x)) {
                return $tail;
            }

            $tail = tail($tail);
        } while ($tail instanceof ListtCons);

        return fromNil();
    })(...func_get_args());
}

/**
 * @var callable
 */
const span = 'Widmogrod\Functional\span';

/**
 * span :: (a -> Bool) -> [a] -> ([a],[a])
 *
 * span _ xs@[]            =  (xs, xs)
 * span p xs@(x:xs')
 * | p x          =  let (ys,zs) = span p xs' in (x:ys,zs)
 * | otherwise    =  ([],xs)
 *
 * span, applied to a predicate p and a list xs, returns a tuple
 * where first element is longest prefix (possibly empty) of xs of elements
 * that satisfy p and second element is the remainder of the list
 *
 * @param  callable       $predicate
 * @param  Listt          $xs
 * @return array|\Closure
 */
function span(callable $predicate, Listt $xs = null)
{
    return curryN(2, function (callable $predicate, Listt $xs): array {
        try {
            $y = head($xs);
            $ys = tail($xs);

            if (!$predicate($y)) {
                return [fromNil(), $xs];
            }

            [$z, $zs] = span($predicate, $ys);

            return [
                prepend($y, $z),
                $zs
            ];
        } catch (EmptyListError $e) {
            return [fromNil(), $xs];
        }
    })(...func_get_args());
}
