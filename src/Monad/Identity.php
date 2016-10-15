<?php
namespace Widmogrod\Monad;

use Widmogrod\Common\PointedTrait;
use Widmogrod\Common\ValueOfInterface;
use Widmogrod\Common\ValueOfTrait;
use Widmogrod\FantasyLand;

class Identity implements
    FantasyLand\Monad ,
    ValueOfInterface
{
    const of = 'Widmogrod\Monad\Identity::of';

    use PointedTrait;
    use ValueOfTrait;

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        return static::of($this->bind($transformation));
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $applicative)
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
