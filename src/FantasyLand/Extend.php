<?php

declare(strict_types=1);

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
