<?php
namespace FantasyLand;

interface FoldableInterface
{
    /**
     * reduce :: (b -> a -> b) -> b -> b
     *
     * @param callable $function    Binary function ($accumulator, $value)
     * @param mixed $accumulator    Value to witch reduce
     * @return mixed                Same type as $accumulator
     */
    public function reduce(callable $function, $accumulator);
}
