<?php
namespace FantasyLand;

interface Extend
{
    /**
     * @param callable $function
     * @return self
     */
    public function extend(callable $function);
}
