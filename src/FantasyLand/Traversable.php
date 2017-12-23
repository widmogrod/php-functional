<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Traversable extends Functor
{
    /**
     * traverse :: Applicative f => (a -> f b) -> f (t b)
     *
     * Where the `a` is value inside of container.
     *
     * @param callable $fn (a -> f b)
     *
     * @return Applicative f (t b)
     */
    public function traverse(callable $fn);
}
