<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

/**
 * @var callable
 */
const identity = 'Widmogrod\Functional\identity';

/**
 * id :: a -> a
 *
 * Return value passed to function
 *
 * @param mixed $x
 *
 * @return mixed
 */
function identity($x)
{
    return $x;
}

/**
 * @var callable
 */
const constt = 'Widmogrod\Functional\constt';

/**
 * const :: a -> b -> a
 *
 * const x is a unary function which evaluates to x for all inputs.
 *
 * For instance,
 * ```haskell
 *  >>> map (const 42) [0..3]
 *      [42,42,42,42]
 * ```
 *
 * @param $a
 * @param $b
 * @return callable
 */
function constt($a, $b = null)
{
    return curryN(2, function ($a) {
        return $a;
    })(...func_get_args());
}


/**
 * @var callable
 */
const compose = 'Widmogrod\Functional\compose';

/**
 * (.) :: (b -> c) -> (a -> b) -> a -> c
 *
 * Compose multiple functions into one.
 * Composition starts from right to left.
 *
 * <code>
 * compose('strtolower', 'strtoupper')('aBc') ≡ 'abc'
 * strtolower(strtouppser('aBc'))  ≡ 'abc'
 * </code>
 *
 * @param callable $a
 * @param callable $b,...
 *
 * @return \Closure func($value) : mixed
 */
function compose(callable $a, callable $b)
{
    return reverse(pipeline)(...func_get_args());
}

/**
 * @var callable
 */
const pipeline = 'Widmogrod\Functional\pipeline';

/**
 * Compose multiple functions into one.
 * Composition starts from left.
 *
 * <code>
 * compose('strtolower', 'strtoupper')('aBc') ≡ 'ABC'
 * strtouppser(strtolower('aBc'))  ≡ 'ABC'
 * </code>
 *
 * @param callable $a
 * @param callable $b,...
 *
 * @return \Closure func($value) : mixed
 */
function pipeline(callable $a, callable $b)
{
    $list = func_get_args();

    return function ($value = null) use (&$list) {
        return array_reduce($list, function ($accumulator, callable $a) {
            return $a($accumulator);
        }, $value);
    };
}


/**
 * @var callable
 */
const flip = 'Widmogrod\Functional\flip';

/**
 * flip :: (a -> b -> c) -> b -> a -> c
 *
 * @param callable $func
 *
 * @return callable
 */
function flip(callable $func)
{
    $args = func_get_args();
    array_shift($args);

    return curryN(2, function ($a, $b) use ($func) {
        $args = func_get_args();
        $args[0] = $b;
        $args[1] = $a;

        return $func(...$args);
    })(...$args);
}
