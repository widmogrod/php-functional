<?php
namespace Monad\Either;

use Monad\CreateTrait;

class Right implements EitherInterface
{
    use CreateTrait;

    const create = 'Monad\Either\Right::create';

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
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     * @return mixed
     */
    public function orElse(callable $fn)
    {
        // Ignore, in the Right monad there is no else
    }

    /**
     * Bind monad value to given $transformation function.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }
}
