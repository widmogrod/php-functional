<?php
namespace FantasyLand;

interface ExtendInterface
{
    /**
     * @param callable $function
     * @return self
     */
    public function extend(callable $function);
}