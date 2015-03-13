<?php
namespace Common;

trait CreateTrait
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Convert value to monad
     *
     * @param mixed $value
     * @return self
     */
    public static function create($value)
    {
        return $value instanceof self
            ? $value
            : new static($value);
    }
}
