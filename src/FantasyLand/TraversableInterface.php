<?php
namespace FantasyLand;

interface TraversableInterface extends FunctorInterface
{
    /**
     * traverse :: ApplicativeInterface f => (a -> f b) -> f (t b)
     *
     * Where the `a` is value inside of container.
     *
     * @param callable $transformation  (a -> f b)
     * @return ApplicativeInterface     f (t b)
     */
    public function traverse(callable $transformation);
}