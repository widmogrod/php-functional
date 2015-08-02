<?php
namespace Monad\Either;

use Common;
use Monad\Feature;

class Right implements
    EitherInterface,
    Feature\LiftInterface
{
    use Common\CreateTrait;

    const create = 'Monad\Either\Right::create';

    /**
     * @inheritdoc
     */
    public function lift(callable $transformation)
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
