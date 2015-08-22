<?php
namespace Monad\IO;

use Functional as f;
use Monad as M;
use Monad\Either as E;

const userError = 'Monad\IO\userError';

/**
 * userError :: String -> IOError
 *
 * @param string $error
 * @return IOError
 */
function userError($error)
{
    return new IOError($error);
}

const throwIO = 'Monad\IO\throwIO';

/**
 * A variant of throw that can only be used within the IO monad.
 *
 * throwIO :: Exception e -> IO a
 *
 * @param \Exception $e
 * @return M\IO
 */
function throwIO(\Exception $e)
{
    return M\IO::of(function () use ($e) {
        throw $e;
    });
}

const tryCatch = 'Monad\IO\tryCatch';

/**
 * tryCatch :: Exception e => IO a -> (e -> IO a) -> IO a
 *
 * @param M\IO $io
 * @param callable $catchFunction
 * @return M\IO
 */
function tryCatch(M\IO $io = null, callable $catchFunction = null)
{
    return call_user_func_array(f\curryN(2, function (M\IO $io, callable $catchFunction) {
        return M\IO::of(function () use ($io, $catchFunction) {
            try {
                return $io->run();
            } catch (\Exception $e) {
                return call_user_func($catchFunction, $e);
            }
        });
    }), func_get_args());
}

const tryEither = 'Monad\IO\tryEither';

/**
 * tryEither :: Exception e => IO a -> IO (Either e a)
 *
 * @param M\IO $io
 * @return M\IO
 */
function tryEither(M\IO $io)
{
    return tryCatch(
        f\bind(E\right, $io),
        E\left
    );
}