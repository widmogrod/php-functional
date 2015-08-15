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
 * @param Either $either
 * @return mixed
 */
function either(callable $succeed, callable $failure, Either $either)
{
    return $either->bimap($failure, $succeed);
}

/**
 * Apply map function on both cases.
 *
 * @return Left|Right
 * @param callable $success
 * @param callable $failure
 * @param Either $either
 */
function doubleMap(callable $success, callable $failure, Either $either)
{
    return either(
        f\compose(succeed(), $success),
        f\compose(fail(), $failure),
        $either
    );
}

/**
 * Adapt function that may throws exceptions to Either monad.
 *
 * @return Either|\Closure
 * @param callable $success
 * @param callable $catchFunction
 * @param mixed $value
 */
function tryCatch(callable $success = null, callable $catchFunction = null, $value = null)
{
    return call_user_func_array(f\curryN(3, function (callable $success, callable $catchFunction, $value) {
        try {
            return succeed(call_user_func($success, $value));
        } catch (\Exception $e) {
            return fail(call_user_func($catchFunction, $e));
        }
    }), func_get_args());
}
