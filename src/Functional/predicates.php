<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

const eql = 'Widmogrod\Functional\eql';

/**
 * eql :: a -> a -> Bool
 *
 * @param mixed $expected
 * @param mixed $value
 *
 * @return bool|\Closure
 */
function eql($expected, $value = null)
{
    return curryN(2, function ($expected, $value): bool {
        return $expected === $value;
    })(...func_get_args());
}

const lt = 'Widmogrod\Functional\lt';

/**
 * lt :: a -> a -> Bool
 *
 * @param mixed $expected
 * @param mixed $value
 *
 * @return bool|\Closure
 */
function lt($expected, $value = null)
{
    return curryN(2, function ($expected, $value): bool {
        return $value < $expected;
    })(...func_get_args());
}


const orr = 'Widmogrod\Functional\orr';

/**
 * orr :: (a -> Bool) -> (a -> Bool) -> a -> Bool
 *
 * @param callable      $predicateA
 * @param callable|null $predicateB
 * @param mixed         $value
 *
 * @return bool|\Closure
 */
function orr(callable $predicateA, callable $predicateB = null, $value = null)
{
    return curryN(3, function (callable $a, callable $b, $value): bool {
        return $a($value) || $b($value);
    })(...func_get_args());
}
