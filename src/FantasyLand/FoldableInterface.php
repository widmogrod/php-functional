<?php
namespace FantasyLand;

interface FoldableInterface
{
    /**
     * @param callable $function    Binary function
     * @param mixed $accumulator
     * @return mixed
     */
    public function reduce(callable $function, $accumulator);
}