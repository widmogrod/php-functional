<?php

declare(strict_types=1);

namespace Widmogrod\Monad\State;

use Widmogrod\Monad as M;

const pure = 'Widmogrod\Monad\State\pure';

/**
 * pure :: Applicative Just f => a -> f a
 *
 * @param callable $f
 *
 * @return M\State
 */
function pure($f)
{
    return M\State::of(function ($state) use ($f) {
        return [$f, $state];
    });
}

const get = 'Widmogrod\Monad\State\get';

/**
 * get :: State s m => m s
 *
 * Return the state from the internals of the monad.
 *
 * @return M\State
 */
function get()
{
    return state(function ($state) {
        return [$state, $state];
    });
}

const put = 'Widmogrod\Monad\State\put';

/**
 * put :: State s m => s -> m ()
 *
 * Replace the state inside the monad.
 *
 * @param mixed $state
 *
 * @return M\State
 */
function put($state)
{
    return state(function () use ($state) {
        return [null, $state];
    });
}

const state = 'Widmogrod\Monad\State\state';

/**
 * state :: State s m => (s -> (a, s)) -> m a
 *
 * Embed a simple state action into the monad.
 *
 * @param callable $stateFunction
 *
 * @return M\State
 */
function state(callable $stateFunction)
{
    return M\State::of(function ($state) use ($stateFunction) {
        return $stateFunction($state);
    });
}

const gets = 'Widmogrod\Monad\State\gets';

/**
 * gets :: State s m => (s -> a) -> m a
 *
 * Gets specific component of the state, using a projection function supplied.
 *
 * @param callable $transformation
 *
 * @return M\State
 */
function gets(callable $transformation)
{
    return M\State::of(function ($state) use ($transformation) {
        return [$transformation($state), $state];
    });
}

const value = 'Widmogrod\Monad\State\value';

/**
 * state :: State s m => a -> m a
 *
 * Put value inside ot the monad
 *
 * @param mixed $value
 *
 * @return M\State
 */
function value($value)
{
    return M\State::of(function ($state) use ($value) {
        return [$value, $state];
    });
}

const modify = 'Widmogrod\Monad\State\modify';

/**
 * modify :: State s m => (s -> s) -> m ()
 *
 * Monadic state transformer.
 *
 * Maps an old state to a new state inside a state monad.
 * The old state is thrown away.
 *
 * @param callable $transformation
 *
 * @return M\State
 */
function modify(callable $transformation)
{
    return M\State::of(function ($state) use ($transformation) {
        return [null, $transformation($state)];
    });
}

const runState = 'Widmogrod\Monad\State\runState';

/**
 * runState :: State s a -> s -> (a, s)
 *
 * Unwrap a state monad computation as a function.
 *
 * @param M\State $state
 * @param mixed   $initialState
 *
 * @return mixed
 */
function runState(M\State $state, $initialState)
{
    return $state->runState($initialState);
}

const evalState = 'Widmogrod\Monad\State\evalState';

/**
 * evalState :: State s a -> s -> a
 *
 * Evaluate a state computation with the given initial state and return the final value, discarding the final state.
 *
 * @param M\State $state
 * @param mixed   $initialState
 *
 * @return mixed
 */
function evalState(M\State $state, $initialState)
{
    return runState($state, $initialState)[0];
}

const execState = 'Widmogrod\Monad\State\execState';

/**
 * execState :: State s a -> s -> s
 *
 * Evaluate a state computation with the given initial state and return the final state, discarding the final value.
 *
 * @param M\State $state
 * @param mixed   $initialState
 *
 * @return mixed
 */
function execState(M\State $state, $initialState)
{
    return runState($state, $initialState)[1];
}
