<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Reader;

use Widmogrod\Monad as M;

const pure = 'Widmogrod\Monad\Reader\pure';

/**
 * pure :: Applicative Just f => a -> f a
 *
 * @param callable $f
 *
 * @return M\Reader
 */
function pure($f)
{
    return M\Reader::of(function () use ($f) {
        return $f;
    });
}

const reader = 'Widmogrod\Monad\Reader\reader';

/**
 * reader :: Reader e m => (e -> a) -> m a
 *
 * Embed a simple reader action into the monad.
 *
 * @param callable $readerFunction
 *
 * @return M\Reader
 */
function reader(callable $readerFunction)
{
    return M\Reader::of(function ($reader) use ($readerFunction) {
        return $readerFunction($reader);
    });
}

const value = 'Widmogrod\Monad\Reader\value';

/**
 * reader :: Reader e m => a -> m a
 *
 * Put value inside ot the monad
 *
 * @param mixed $value
 *
 * @return M\Reader
 */
function value($value)
{
    return M\Reader::of(function () use ($value) {
        return $value;
    });
}

const runReader = 'Widmogrod\Monad\Reader\runReader';

/**
 * runReader :: Reader e a -> e -> a
 *
 * Unwrap a reader monad computation as a function.
 *
 * @param M\Reader $reader
 * @param mixed    $env
 *
 * @return mixed
 */
function runReader(M\Reader $reader, $env)
{
    return $reader->runReader($env);
}
