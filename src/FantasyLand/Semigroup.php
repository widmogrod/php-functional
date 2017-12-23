<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Semigroup
{
    /**
     * Return result of applying one semigroup with another.
     *
     * @param Semigroup $value
     *
     * @return self
     */
    public function concat(self $value): self;
}
