<?php
namespace Monad\Control;

use Monad as M;
use Functional as f;

/**
 * doo :: Monad m => [m a] -> [a] -> m a
 *
 * Haskell like "do notation" simple implementation.
 * Since "do" is reserved keyword in PHP then I use "doo".
 *
 * @param array $monads
 * @param array $accumulator
 * @return mixed
 */
function doo(array $monads, array $accumulator = [])
{
    reset($monads);
    list($key, $monad) = each($monads);

    $step = function ($value) use ($key, $accumulator, $monads) {
        if ($value instanceof \Closure) {
            $value = call_user_func($value, $accumulator);
        }

        $accumulator[$key] = $value;

        return count($monads) > 1
            ? doo(f\tail($monads), $accumulator)
            : $value;
    };

    return $monad->bind($step);
}

/**
 * runWith :: Monad m => (a -> m b) -> [a] -> m b
 *
 * @param callable $function
 * @param array $argsNames
 * @return M\IO
 */
function runWith(callable $function, array $argsNames) {
    return M\IO::of(function() use ($function, $argsNames) {
        return function($data) use ($function, $argsNames) {
            return call_user_func_array(
                $function,
                array_reduce($argsNames, function($base, $index) use ($data) {
                    $base[$index] = $data[$index];
                    return $base;
                }, [])
            );
        };
    });
};
