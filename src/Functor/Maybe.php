<?php
namespace Functor;

use Common;

class Maybe implements FunctorInterface, Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;

    const create = 'Functor\Maybe::create';

    /**
     * Transforms one category into another category.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function map(callable $transformation)
    {
        if (null === $this->value) {
            return $this;
        }

        return static::create(call_user_func($transformation, $this->value));
    }
}
