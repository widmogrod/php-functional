<?php
namespace FantasyLand;

interface PointedInterface
{
    /**
     * Put $value in default minimal context.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function of($value);
}
