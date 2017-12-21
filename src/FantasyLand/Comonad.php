<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Comonad extends
    Functor,
    Extend
{
    public function extract();
}
