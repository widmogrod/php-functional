<?php

namespace Widmogrod\FantasyLand;

interface Traversable extends Functor
{
    /**
     * traverse :: Applicative f => (a -> f b) -> f (t b)
     *
     * Where the `a` is value inside of container.
     *
     * @param callable $transformation  (a -> f b)
     *
     * @return Applicative     f (t b)
     */
    public function traverse(callable $transformation);
}
