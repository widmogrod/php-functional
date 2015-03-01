<?php
namespace Monad;

use Exception;

class Collection implements
    MonadInterface,
    Feature\LiftInterface,
    Feature\ValueOfInterface
{
    use CreateTrait;

    const create = 'Monad\Collection::create';

    /**
     * @var array|\Traversable
     */
    private $traversable;

    /**
     * Ensure everything on start.
     *
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
            $result[$index] = $value instanceof MonadInterface
                ? $value->bind($transformation)
                : call_user_func($transformation, $value, $index);
        }

        return $result;
    }

    /**
     * Converts values returned by regular function to monadic value.
     *
     * @param callable $transformation
     * @return Collection
     */
    public function lift(callable $transformation)
    {
        $result = [];
        foreach ($this->traversable as $index => $value) {
            $result[$index] = $value instanceof MonadInterface
                    ? Utils::lift($value, $transformation)
                    : call_user_func($transformation, $value, $index);
        }

        return $this::create($result);
    }

    /**
     * Return value wrapped by Monad
     *
     * @return array
     */
    public function valueOf()
    {
        return array_map(function($value) {
            return $value instanceof Feature\ValueOfInterface
                ? $value->valueOf()
                : $value;
        }, $this->traversable);
    }
}
