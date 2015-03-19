<?php
namespace Functor;

use Common;

class Collection implements FunctorInterface, Common\ValueOfInterface
{
    use Common\CreateTrait;

    const create = 'Functor\Collection::create';

    /**
     * @param array $value
     */
    public function __construct($value)
    {
        $this->value = is_array($value) || $value instanceof \Traversable
            ? $value
            : [$value];
    }

    /**
     * Transforms one category into another category.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function map(callable $transformation)
    {
        $result = [];
        foreach ($this->value as $key => $value) {
            $result[$key] = call_user_func($transformation, $value);
        }

        return self::create($result);
    }

    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function valueOf()
    {
        return array_map(function ($value) {
            return $value instanceof Common\ValueOfInterface
                ? $value->valueOf()
                : $value;
        }, $this->value);
    }
}