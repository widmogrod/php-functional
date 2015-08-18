<?php
namespace Monad\Maybe;

use Functional as f;

/**
 * @return Nothing
 */
function nothing()
{
    return Nothing::of(null);
}

const just = 'Monad\Maybe\just';

/**
 * @return Just
 * @param mixed $value
 */
function just($value)
{
    return Just::of($value);
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
