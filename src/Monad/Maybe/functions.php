<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Maybe;

use Widmogrod\Functional as f;

const pure = 'Widmogrod\Monad\Maybe\pure';

/**
 * pure :: Applicative Just f => a -> f a
 *
 * @param callable $f
 *
 * @return Just
 */
function pure($f)
{
    return Just::of($f);
}

const nothing = 'Widmogrod\Monad\Maybe\nothing';

/**
 * @return Nothing
 */
function nothing()
{
    return Nothing::of(null);
}

const just = 'Widmogrod\Monad\Maybe\just';

/**
 * @return Just
 *
 * @param mixed $value
 */
function just($value)
{
    return Just::of($value);
}

const maybe = 'Widmogrod\Monad\Maybe\maybe';

/**
 * maybe :: b -> (a -> b) -> Maybe a -> b
 *
 * @param null     $default
 * @param callable $fn
 * @param Maybe    $maybe
 *
 * @return mixed|\Closure
 */
function maybe($default, callable $fn = null, Maybe $maybe = null)
{
    return f\curryN(3, function ($default, callable $fn, Maybe $maybe) {
        if ($maybe instanceof Nothing) {
            return $default;
        }

        return $fn($maybe->extract());
    })(...func_get_args());
}

const maybeNull = 'Widmogrod\Monad\Maybe\maybeNull';

/**
 * Create maybe for value
 *
 * maybeNull :: a -> Maybe a
 *
 * @param mixed|null
 *
 * @return Maybe
 */
function maybeNull($value = null)
{
    return null === $value
        ? nothing()
        : just($value);
}

const fromMaybe = 'Widmogrod\Monad\Maybe\fromMaybe';

/**
 * Open $maybe monad
 *
 * fromMaybe :: a -> Maybe a -> a
 *
 * @param mixed $default
 * @param Maybe $maybe
 *
 * @return mixed
 */
function fromMaybe($default = null, Maybe $maybe = null)
{
    return f\curryN(2, function ($default, Maybe $maybe) {
        return maybe($default, f\identity, $maybe);
    })(...func_get_args());
}
