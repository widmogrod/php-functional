<?php

declare(strict_types=1);

namespace Widmogrod\FantasyLand;

interface Pointed
{
    /**
     * Put $value in default minimal context.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function of($value);
}
