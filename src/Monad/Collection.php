<?php
namespace Monad;

use Exception;

class Collection implements
    MonadInterface,
    LiftInterface
{
    use CreateTrait;
    use LiftTrait;

    const create = 'Monad\Collection::create';

    /**
     * @var array|\Traversable
     */
    private $traversable;

    /**
     * @param array|\Traversable $traversable
     * @throws Exception\InvalidTypeException
     */
    public function __construct($traversable)
    {
        Exception\InvalidTypeException::assertIsTraversable($traversable);

        $this->traversable = $traversable;
    }

    /**
     * Bind monad value to given $transformation function
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation)
    {
        $result = [];
        foreach ($this->traversable as $index => $value) {
            $result[] = call_user_func($transformation, $value, $index);
        }

        return $result;
    }
}
