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

const maybeNull = 'Monad\Maybe\maybeNull';

/**
 * Create maybe for value
 *
 * @param mixed|null
 * @return Maybe
 */
function maybeNull($value = null)
{
    return null === $value
        ? nothing()
        : just($value);
}

const fromMaybe = 'Monad\Maybe\fromMaybe';

/**
 * Open $maybe monad
 *
 * fromMaybe :: a -> Maybe a -> a
 *
 * @param mixed $default
 * @param Maybe $maybe
 * @return mixed
 */
function fromMaybe($default = null, Maybe $maybe = null) {
    return call_user_func_array(f\curryN(2, function($default, Maybe $maybe) {
        if ($maybe instanceof Nothing) {
            return $default;
        }

        return f\join($maybe);
    }), func_get_args());
}
