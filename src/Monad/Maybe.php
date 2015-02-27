<?php
namespace Monad;

class Maybe implements
    MonadInterface,
    LiftInterface
{
    use CreateTrait;
    use LiftTrait;

    const create = 'Monad\Maybe::create';

    /**
     * @var
     */
    private $value;

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
