<?php
namespace Monad\Either;

use Common;
use Monad\Feature;

class Left implements
    EitherInterface,
    Feature\LiftInterface
{
    use Common\CreateTrait;

    const create = 'Monad\Either\Left::create';

    /**
     * @inheritdoc
     */
    public function lift(callable $transformation)
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
