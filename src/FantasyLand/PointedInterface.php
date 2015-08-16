<?php
namespace FantasyLand;

interface PointedInterface
{
    /**
     * Put value $b in default minimal context.
     *
     * @param mixed $b
     * @return self
     */
    public static function of($b);
}
