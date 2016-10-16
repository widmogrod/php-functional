<?php
namespace Widmogrod\FantasyLand;

interface Setoid
{
    /**
     * @param Setoid|mixed $value
     * @return boolean
     */
    public function equals($value);
}
