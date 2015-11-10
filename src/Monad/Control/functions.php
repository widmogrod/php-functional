<?php
namespace Monad\Control;

use Functional as f;
use Monad as M;

/**
 * doo :: State IO m => [m a] -> m a
 *
 * Haskell like "do notation" simple implementation.
 * Since "do" is reserved keyword in PHP then I use "doo".
 *
 * @param array|M\IO[] $monads
 * @return M\IO
 */
function doo(array $monads)
{
    return M\IO::of(function () use ($monads) {
        $result = null;
        $data = [];
        // TODO do it by foldWithKeys
        foreach ($monads as $key => $monad) {
            // TODO do it better - maybe?
            if ($monad instanceof M\IO) {
                $monad = ioState($monad);
            }

            $state = [$key, $data];
            list($result, list(, $data)) = M\State\runState($monad, $state);
        }

        return $result;
    });
}

/**
 * runWith :: (a -> IO b) -> [a] -> State IO b
 *
 * @param callable $function
 * @param array $argsNames
 * @return M\State
 */
function runWith(callable $function, array $argsNames)
{
    return M\State::of(function (array $state) use ($function, $argsNames) {
        list ($key, $data) = $state;

        $args = array_reduce($argsNames, function ($base, $index) use ($data) {
            $base[$index] = $data[$index];
            return $base;
        }, []);

        $value = call_user_func_array(
            $function,
            $args
        )->run();

        $data[$key] = $value;
        $newState = [$key, $data];

        return [$value, $newState];
    });
}

/**
 * ioState :: IO a -> State IO a
 *
 * @param M\IO $io
 * @return M\State
 */
function ioState(M\IO $io)
{
    return M\State::of(function ($state) use ($io) {
        list ($key, $data) = $state;

        $value = $io->run();

        $data[$key] = $value;
        $newState = [$key, $data];

        return [$value, $newState];
    });
}
