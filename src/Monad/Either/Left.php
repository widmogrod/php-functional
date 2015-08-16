<?php
namespace Monad\Either;

use Common;
use Functor;

class Left implements Either
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
    public function bimap(callable $left, callable $right)
    {
        return self::create(call_user_func($left, $this->value));
    }
}
