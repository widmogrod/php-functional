<?php
namespace Widmogrod\Monad\Maybe;

use Widmogrod\Common\PointedTrait;
use Widmogrod\Monad;
use Widmogrod\FantasyLand;

class Just implements Maybe
{
    use PointedTrait;

    const of = 'Widmogrod\Monad\Maybe\Just::of';

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
    public function map(callable $transformation)
    {
        return self::of($this->bind($transformation));
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
    public function orElse(callable $fn)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return $this->value;
    }
}
