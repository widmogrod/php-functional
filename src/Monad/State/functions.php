<?php
namespace Monad\State;

use Monad as M;


// put :: s -> m ()
function put($state)
{
    return M\State::of(function () use ($state) {
        return [null, $state];
    });
}


// get :: m s
function get()
{
    return M\State::of(function ($s) {
        return [$s, $s];
    });
}

// gets :: MonadState s m => (s -> a) -> m a
function gets(callable $transformation)
{
    return M\State::of(function ($state) use ($transformation) {
        return [call_user_func($transformation, $state), $state];
    });
}

// state :: (s -> (a, s)) -> m a
function state1(callable $stateFunction)
{
    return M\State::of(function ($state) use ($stateFunction) {
        return call_user_func($stateFunction, $state);
    });
}

// state :: a -> State (s, a)
function state($value)
{
    return M\State::of(function ($state) use ($value) {
        return [$value, $state];
    });
}

// modify :: MonadState s m => (s -> s) -> m ()
// modify :: (s -> s) -> m ()
function modify(callable $transformation)
{
    return M\State::of(function ($state) use ($transformation) {
        return [null, $transformation($state)];
    });
}