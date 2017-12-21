<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Apply extends Functor
{
    /**
     * @param Apply $b
     *
     * @return self
     */
    public function ap(self $b);
}
