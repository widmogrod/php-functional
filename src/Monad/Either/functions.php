<?php
namespace Monad\Either;

use Functional as f;

const right = 'Monad\Either\right';

/**
 * @param mixed $value
 * @return Right
 */
function right($value)
{
    return Right::of($value);
}

const left = 'Monad\Either\left';

/**
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
 * @param callable $left
 * @param callable $right
 * @param Either $either
 * @return mixed
 */
function eitherF(callable $left, callable $right, Either $either)
{
    return $either->bimap($left, $right);
}

/**
 * Apply either a success function or failure function
 *
 * @param callable $left
 * @param callable $right
 * @param Either $either
 * @return mixed
 */
function either(callable $left = null, callable $right, Either $either = null)
{
    return call_user_func_array(
        f\curryN(3, 'Monad\Either\eitherF'),
        func_get_args()
    );
}

/**
 * Apply map function on both cases.
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
    return call_user_func_array(f\curryN(3, function (callable $success, callable $catchFunction, $value) {
        try {
            return right(call_user_func($success, $value));
        } catch (\Exception $e) {
            return left(call_user_func($catchFunction, $e));
        }
    }), func_get_args());
}
