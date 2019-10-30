<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Either;

use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe;

const pure = 'Widmogrod\Monad\Either\pure';

/**
 * pure :: Applicative Either f => a -> f a
 *
 * @param callable $f
 *
 * @return Right
 */
function pure($f)
{
    return Right::of($f);
}

const right = 'Widmogrod\Monad\Either\right';

/**
 * right :: a -> Right a
 *
 * @param mixed $value
 *
 * @return Right
 */
function right($value)
{
    return Right::of($value);
}

const left = 'Widmogrod\Monad\Either\left';

/**
 * left :: a -> Left a
 *
 * @param mixed $value
 *
 * @return Left
 */
function left($value)
{
    return Left::of($value);
}

const either = 'Widmogrod\Monad\Either\either';

/**
 * Apply either a success function or failure function
 *
 * either :: (a -> c) -> (b -> c) -> Either a b -> c
 *
 * @param callable $left   (a -> c)
 * @param callable $right  (b -> c)
 * @param Either   $either Either a b
 *
 * @return mixed c
 */
function either(callable $left, callable $right = null, Either $either = null)
{
    return f\curryN(3, function (callable $left, callable $right, Either $either) {
        return $either->either($left, $right);
    })(...func_get_args());
}

const doubleMap = 'Widmogrod\Monad\Either\doubleMap';

/**
 * Apply map function on both cases.
 *
 * doubleMap :: (a -> c) -> (b -> d) -> Either a b -> Either c d
 *
 * @param callable $left
 * @param callable $right
 * @param Either   $either
 *
 * @return Left|Right|\Closure
 */
function doubleMap(callable $left, callable $right = null, Either $either = null)
{
    return f\curryN(3, function (callable $left, callable $right, Either $either) {
        return either(
            f\compose(left, $left),
            f\compose(right, $right),
            $either
        );
    })(...func_get_args());
}

const tryCatch = 'Widmogrod\Monad\Either\tryCatch';

/**
 * Adapt function that may throws exceptions to Either monad.
 *
 * tryCatch :: Exception e => (a -> b) -> (e -> c) -> a -> Either c b
 *
 * @param callable $function      (a -> b)
 * @param callable $catchFunction (e -> c)
 * @param mixed    $value         a
 *
 * @return Either|\Closure
 */
function tryCatch(callable $function = null, callable $catchFunction = null, $value = null)
{
    return f\curryN(3, function (callable $function, callable $catchFunction, $value) {
        return f\tryCatch(
            f\compose(right, $function),
            f\compose(left, $catchFunction),
            $value
        );
    })(...func_get_args());
}

const toMaybe = 'Widmogrod\Monad\Either\toMaybe';

/**
 * toMaybe :: Either x a -> Maybe a
 *
 * @param Either $either
 *
 * @return Maybe\Maybe
 */
function toMaybe(Either $either)
{
    return either(Maybe\nothing, Maybe\just, $either);
}

const fromLeft = 'Widmogrod\Monad\Either\fromLeft';

/**
 * fromLeft :: a -> Either a b -> a
 *
 * @param  mixed  $a
 * @param  Either $either
 * @return mixed
 */
function fromLeft($a, Either $either = null)
{
    return f\curryN(2, function ($a, Either $either = null) {
        return either(f\identity, f\constt($a), $either);
    })(...func_get_args());
}

const fromRight = 'Widmogrod\Monad\Either\fromRight';

/**
 * fromRight :: b -> Either a b -> b
 *
 * @param  mixed  $a
 * @param  Either $either
 * @return mixed
 */
function fromRight($a, Either $either = null)
{
    return f\curryN(2, function ($a, Either $either = null) {
        return either(f\constt($a), f\identity, $either);
    })(...func_get_args());
}
