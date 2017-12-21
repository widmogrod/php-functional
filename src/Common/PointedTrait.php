<?php

declare(strict_types=1);

namespace Widmogrod\Common;

trait PointedTrait
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * Ensure everything on start.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public static function of($value)
    {
        return new static($value);
    }
}
