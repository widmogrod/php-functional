<?php
namespace Monad\Either;

use Common;
use Functor;

class Left implements
    EitherInterface,
    Functor\FunctorInterface,
    Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;

    const create = 'Monad\Either\Left::create';

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        // Don't do anything
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orElse(callable $fn)
    {
        return call_user_func($fn, $this->value);
    }
}
