<?php
namespace Monad\Maybe;

use Functional as f;

/**
 * @return Nothing
 */
function nothing()
{
    return Nothing::create(null);
}

/**
 * @return Just
 * @param mixed $value
 */
function just($value = null)
{
    return call_user_func_array(
        f\curryN(1, Just::create),
        func_get_args()
    );
}


/**
 * Create maybe for value
 *
 * @param mixed|null
 * @return Maybe
 */
function maybeNullF($value)
{
    return null === $value
        ? nothing()
        : just($value);
}

/**
 * Curried version of maybeF
 *
 * @param mixed|null
 * @return Maybe
 */
function maybeNull($value = null)
{
    return call_user_func_array(
        f\curryN(1, 'Monad\Maybe\maybeNullF'),
        func_get_args()
    );
}
