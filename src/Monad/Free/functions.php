<?php

namespace Widmogrod\Monad\Free;

use Widmogrod\Functional as f;

const liftF = 'Widmogrod\Monad\Free\liftF';

/**
 * @param mixed $value
 *
 * @return Free
 */
function liftF($value)
{
    return Free::of(Pure::of, $value);
}

const runFree = 'Widmogrod\Monad\Free\runFree';

/**
 * runFree :: Monad m => (a -> m b) -> MonadFree f a -> m b
 *
 * @param callable $interpretation Monad m => (a -> m b) -> m b
 * @param MonadFree $free
 *
 * @return mixed
 */
function runFree(callable $interpretation, MonadFree $free = null)
{
    return call_user_func_array(f\curryN(2, function (callable $interpretation, MonadFree $free) {
        return $free->runFree($interpretation);
    }), func_get_args());
}
