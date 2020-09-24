<?php

declare(strict_types=1);

namespace Widmogrod\Useful;

use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\concatM;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\fromValue;
use function Widmogrod\Functional\zip;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

const any = 'Widmogrod\Useful\PatternAny';

/**
 * match :: #{ Pattern -> (a -> b)} -> a -> b
 *
 * @param  array                  $patterns
 * @param  mixed                  $value
 * @throws PatternNotMatchedError
 *
 * @return mixed
 */
function matchPatterns(array $patterns, $value = null)
{
    return curryN(2, function (array $patterns, $value) {
        if (count($patterns) === 0) {
            throw PatternNotMatchedError::noPatterns($value);
        }

        foreach ($patterns as $className => $fn) {
            $isTuplePattern = is_int($className) && is_array($fn);
            if ($isTuplePattern) {
                [$tuple, $fn] = $fn;
                $result = matchTuple($tuple, $value);
                if ($result instanceof Just) {
                    return $fn(...$result->extract());
                }
            }

            if (isMatch($value, $className)) {
                return isAny($className)
                    ? $fn($value)
                    : matchApply($value, $fn);
            }
        }

        throw PatternNotMatchedError::cannotMatch($value, array_keys($patterns));
    })(...func_get_args());
}

function isMatch($value, $className): bool
{
    if (is_array($value)) {
        return false;
    }

    if ($value instanceof $className) {
        return true;
    }

    return isAny($className);
}

function isAny($className)
{
    return $className === any;
}

function matchTuple(array $tuplePattern, array $valueTuple)
{
    $patternCount = count($tuplePattern);
    $valueCount = count($valueTuple);

    if ($valueCount !== $patternCount) {
        return nothing();
    }

    $collectArgs = function (): Listt {
        return fromIterable(func_get_args());
    };

    $args = fromNil();
    foreach (zip(fromIterable($tuplePattern), fromIterable($valueTuple)) as [$className, $value]) {
        if (!isMatch($value, $className)) {
            return nothing();
        }

        $args = concatM($args, isAny($className) ? fromValue($value) : matchApply($value, $collectArgs));
    }

    return just(iterator_to_array($args));
}

function matchApply($value, callable $fn)
{
    return $value instanceof PatternMatcher
        ? $value->patternMatched($fn)
        : $fn($value);
}
