<?php
namespace FantasyLand;

interface PointedInterface
{
    /**
     * Put $value in default minimal context.
     *
     * @param mixed $value
     * @return self
     */
    public static function of($value);
}
