<?php
namespace Widmogrod\FantasyLand;

interface Setoid
{
    /**
     * @param Setoid|mixed $other
     * @return boolean
     */
    public function equals($other);
}
