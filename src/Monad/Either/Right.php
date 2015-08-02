<?php
namespace Monad\Either;

use Common;
use Functor;

class Right implements
    EitherInterface,
    Functor\FunctorInterface,
    Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;

    const create = 'Monad\Either\Right::create';

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        return self::create($this->bind($transformation));
    }

    /**
     * @inheritdoc
     */
    public function orElse(callable $fn)
    {
        return $this;
        // Ignore, in the Right monad there is no else
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }
}
