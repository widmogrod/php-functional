<?php
namespace Monad\Either;

use Functional as f;

const right = 'Monad\Either\right';

/**
 * right :: a -> Right a
 *
 * @param mixed $value
 * @return Right
 */
function right($value)
{
    return Right::of($value);
}

const left = 'Monad\Either\left';

/**
 * left :: a -> Left a
 *
 * @param mixed $value
 * @return Left
 */
function left($value)
{
    return Left::of($value);
}

/**
 * Apply either a success function or failure function
 *
 * either :: (a -> c) -> (b -> c) -> Either a b -> c
 *
 * @param callable $left
 * @param callable $right
 * @param Either $either
 * @return mixed
 */
function either(callable $left = null, callable $right = null, Either $either = null)
{
    return call_user_func_array(f\curryN(3, function (callable $left, callable $right, Either $either) {
        return $either->bimap($left, $right);
    }), func_get_args());
}

/**
 * Apply map function on both cases.
 *
 * doubleMap :: (a -> c) -> (b -> d) -> Either a b -> Either c d
 *
 * @return Left|Right
 * @param callable $left
 * @param callable $right
 * @param Either $either
 */
function doubleMap(callable $left, callable $right, Either $either)
{
    return either(
        f\compose(left, $left),
        f\compose(right, $right),
        $either
    );
}

/**
 * Adapt function that may throws exceptions to Either monad.
 *
 * tryCatch :: Exception e => (a -> b) -> (e -> c) -> a -> Either c b
 *
 * @return Either|\Closure
 * @param callable $function
 * @param callable $catchFunction
 * @param mixed $value
 */
function tryCatch(callable $function = null, callable $catchFunction = null, $value = null)
{
    return call_user_func_array(f\curryN(3, function (callable $function, callable $catchFunction, $value) {
        return f\tryCatch(
            f\compose(right, $function),
            f\compose(left, $catchFunction),
            $value
        );
    }), func_get_args());
}
