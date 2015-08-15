<?php
namespace Monad;

use Common;
use FantasyLand;

class Identity implements
    FantasyLand\MonadInterface,
    Common\ValueOfInterface
{
    const create = 'Monad\Identity::create';

    use Common\CreateTrait;
    use Common\ValueOfTrait;

    public static function of(callable $b)
    {
        return self::create($b);
    }

    public function map(callable $transformation)
    {
        return static::create(call_user_func($transformation, $this->value));
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

    public function valueOf()
    {
        return $this->value;
    }
}
