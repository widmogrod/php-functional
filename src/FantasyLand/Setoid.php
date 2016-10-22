<?php

namespace Widmogrod\FantasyLand;

interface Setoid
{
    /**
     * @param Setoid|mixed $other
     *
     * @return bool
     */
    public function equals($other);
}
