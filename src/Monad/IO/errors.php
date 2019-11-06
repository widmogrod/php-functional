<?php

declare(strict_types=1);

namespace Widmogrod\Monad\IO;

use Widmogrod\Functional as f;
use Widmogrod\Monad as M;
use Widmogrod\Monad\Either as E;

const userError = 'Widmogrod\Monad\IO\userError';

/**
 * userError :: String -> IOError
 *
 * @param string $error
 *
 * @return IOError
 */
function userError($error)
{
    return new IOError($error);
}

const throwIO = 'Widmogrod\Monad\IO\throwIO';

/**
 * A variant of throw that can only be used within the IO monad.
 *
 * throwIO :: Exception e -> IO a
 *
 * @param \Exception $e
 *
 * @return M\IO
 */
function throwIO(\Exception $e)
{
    return M\IO::of(function () use ($e) {
        throw $e;
    });
}

const tryCatch = 'Widmogrod\Monad\IO\tryCatch';

/**
 * tryCatch :: Exception e => IO a -> (e -> IO a) -> IO a
 *
 * @param M\IO     $io
 * @param callable $catchFunction
 *
 * @return M\IO|\Closure
 */
function tryCatch(M\IO $io = null, callable $catchFunction = null)
{
    return f\curryN(2, function (M\IO $io, callable $catchFunction) {
        return M\IO::of(function () use ($io, $catchFunction) {
            try {
                return $io->run();
            } catch (\Exception $e) {
                return $catchFunction($e);
            }
        });
    })(...func_get_args());
}

const tryEither = 'Widmogrod\Monad\IO\tryEither';

/**
 * tryEither :: Exception e => IO a -> IO (Either e a)
 *
 * @param M\IO $io
 *
 * @return M\IO
 */
function tryEither(M\IO $io)
{
    return tryCatch(
        f\bind(E\right, $io),
        E\left
    );
}

/**
 * tryMaybe :: IO a -> IO (Maybe a)
 *
 * @param M\IO $io
 *
 * @return M\IO
 */
function tryMaybe(M\IO $io)
{
    return tryCatch(
        f\bind(M\Maybe\just, $io),
        M\Maybe\nothing
    );
}
