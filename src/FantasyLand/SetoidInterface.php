<?php
namespace FantasyLand;

interface SetoidInterface
{
    /**
     * @param SetoidInterface|mixed $value
     * @return boolean
     */
    public function equals($value);
}