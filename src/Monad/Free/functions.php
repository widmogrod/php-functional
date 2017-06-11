<?php

namespace Widmogrod\Monad\Free;

// liftF :: (Functor f, Monad m) => f a -> FreeT f m a
function liftF($value)
{
    return Free::of(Pure::of, $value);
}
