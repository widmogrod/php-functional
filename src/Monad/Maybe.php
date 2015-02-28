<?php
namespace Monad;

class Maybe implements
    MonadInterface,
    LiftInterface,
    Feature\ValueOfInterface
{
    use CreateTrait;
    use LiftTrait;
    use ValueOfTrait;

    const create = 'Monad\Maybe::create';

    /**
     * @var mixed
     */
    private $value;

    /**
     * Ensure everything on start.
     *
     * @param $value
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
        if (null === $this->value) {
            return null;
        }

        return call_user_func($transformation, $this->value);
    }
}
