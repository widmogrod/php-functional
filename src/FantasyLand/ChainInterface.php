<?php
namespace FantasyLand;

interface ChainInterface extends ApplyInterface
{
    /**
     * @param callable $function
     * @return self
     */
    public function chain(callable $function);
}