<?php
namespace Common;

interface CreateInterface 
{
    /**
     * Convert value to a new context.
     *
     * @param mixed $value
     * @return self
     */
    public static function create($value);
}
