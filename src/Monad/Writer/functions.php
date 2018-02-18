<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Writer;

use FunctionalPHP\FantasyLand;
use Widmogrod\Monad as M;

const pure = 'Widmogrod\Monad\Writer\pure';

/**
 * pure :: Applicative Writer f => a -> f a
 *
 * @param mixed $f
 *
 * @return M\Writer
 */
function pure($f)
{
    return M\Writer::of($f);
}

const value = 'Widmogrod\Monad\Writer\value';

/**
 * value :: Writer w m => a -> m a
 *
 * Replace the value inside the monad
 *
 * @param mixed $value
 *
 * @return callable
 */
function value($value)
{
    return function () use ($value) {
        return M\Writer::of($value);
    };
}

const log = 'Widmogrod\Monad\Writer\log';

/**
 * log :: Writer s w => w -> m a
 *
 * Add a log to the writer without modifying the value
 *
 * @param FantasyLand\Monoid $log
 *
 * @return callable
 */
function log(FantasyLand\Monoid $log)
{
    return function ($value) use ($log) {
        return M\Writer::of($value, $log);
    };
}

const runWriter = 'Widmogrod\Monad\Writer\runWriter';

/**
 * runWriter :: Writer w a -> (a, w)
 *
 * Unwrap a writer monad computation as a function.
 *
 * @param M\Writer $writer
 *
 * @return mixed
 */
function runWriter(M\Writer $writer)
{
    return $writer->runWriter();
}

const evalWriter = 'Widmogrod\Monad\Writer\evalWriter';

/**
 * evalWriter :: Writer w a -> a
 *
 * Evaluate a writer computation with the given initial writer and return the final value, discarding the final writer.
 *
 * @param M\Writer $writer
 *
 * @return mixed
 */
function evalWriter(M\Writer $writer)
{
    return runWriter($writer)[0];
}

const execWriter = 'Widmogrod\Monad\Writer\execWriter';

/**
 * execWriter :: Writer w a -> w
 *
 * Evaluate a writer computation with the given initial writer and return the final side value, discarding the final value.
 *
 * @param M\Writer $writer
 *
 * @return mixed
 */
function execWriter(M\Writer $writer)
{
    return runWriter($writer)[1];
}
