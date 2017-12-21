<?php

namespace Widmogrod\Useful;

use function Widmogrod\Functional\curryN;

/**
 * match :: #{ Pattern -> (a -> b)} -> a -> b
 *
 * @param array $patterns
 * @param mixed $value
 * @throws PatternNotMatchedError
 *
 * @return mixed
 */
function match(array $patterns, $value = null)
{
    return curryN(2, function (array $patterns, $value) {
        if (count($patterns) === 0) {
            throw PatternNotMatchedError::noPatterns($value);
        }

        foreach ($patterns as $className => $fn) {
            if ($value instanceof $className) {
                return $value instanceof PatternMatcher
                    ? $value->patternMatched($fn)
                    : $fn($value);
            }
        }

        throw PatternNotMatchedError::cannotMatch($value, array_keys($patterns));
    })(...func_get_args());
}
