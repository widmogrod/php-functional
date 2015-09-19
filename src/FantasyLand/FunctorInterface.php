<?php
namespace FantasyLand;

interface FunctorInterface
{
    /**
     * @param callable $function
     * @return self
     */
    public function map(callable $function);
}