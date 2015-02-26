<?php
namespace Monad;

class Unit implements MonadInterface
{
    use CreateTrait;

    const create = 'Monad\Unit::create';

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Bind monad value to given $transformation function
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }
}
