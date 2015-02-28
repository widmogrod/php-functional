<?php
namespace Monad;

class Unit implements
    MonadInterface,
    LiftInterface,
    ValueOfInterface
{
    use CreateTrait;
    use LiftTrait;
    use ValueOfTrait;

    const create = 'Monad\Unit::create';

    /**
     * @var mixed
     */
    private $value;

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
