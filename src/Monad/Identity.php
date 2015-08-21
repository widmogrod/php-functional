<?php
namespace Monad;

use Common;
use FantasyLand;

class Identity implements
    FantasyLand\MonadInterface,
    Common\ValueOfInterface
{
    const of = 'Monad\Identity::of';

    use Common\PointedTrait;
    use Common\ValueOfTrait;

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        return static::of(call_user_func($transformation, $this->value));
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\ApplyInterface $applicative)
    {
        return $applicative->map($this->value);
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return $this->value;
    }
}
