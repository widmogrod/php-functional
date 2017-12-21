<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Functor
{
    /**
     * @param callable $function
     *
     * @return self
     */
    public function map(callable $function);
}
