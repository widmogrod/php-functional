<?php
namespace Common;

trait CreateTrait
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Convert value to monad
     *
     * @param mixed $value
     * @return static
     */
    public static function create($value)
    {
        return $value instanceof static
            ? $value
            : new static($value);
    }
}
