<?php

namespace Widmogrod\FantasyLand;

interface Extend
{
    /**
     * @param callable $function
     *
     * @return self
     */
    public function extend(callable $function);
}
