<?php
namespace Monad\Either;

use Functional as f;

/**
 * @param mixed $value
 * @return Right|\Closure
 */
function succeed($value = null)
{
    return call_user_func_array(f\curryN(1, Right::create), func_get_args());
}

/**
 * @param mixed $value
 * @return Left|\Closure
 */
function fail($value = null)
{
    return call_user_func_array(f\curryN(1, Left::create), func_get_args());
}

/**
 * Apply either a success function or failure function
 *
 * @param callable $succeed
 * @param callable $failure
 * @param EitherInterface $either
 * @return mixed
 */
function either(callable $succeed, callable $failure, EitherInterface $either)
{
    if ($either instanceof Right) {
        return $either->bind($succeed);
    } else {
        return $either->orElse($failure);
    }
}

function bind(callable $function, EitherInterface $value)
{
    return either($function, fail(), $value);
}

/**
 * Adapt function that may throws exceptions to Either monad.
 *
 * @return EitherInterface|\Closure
 * @param callable $function
 * @param callable $catchFunction
 * @param mixed $value
 */
function tryCatch(callable $function = null, callable $catchFunction = null, $value = null)
{
    return call_user_func_array(f\curryN(3, function (callable $function, callable $catchFunction, $value) {
        try {
            return succeed(call_user_func($function, $value));
        } catch (\Exception $e) {
            return fail(call_user_func($catchFunction, $e));
        }
    }), func_get_args());
}
