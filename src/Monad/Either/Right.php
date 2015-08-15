<?php
namespace Monad\Either;

use Common;
use Functor;
use FantasyLand;

class Right implements Either
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;

    const create = 'Monad\Either\Right::create';

    public static function of(callable $b)
    {
        return self::create($b);
    }

    public function ap(FantasyLand\ApplyInterface $b)
    {
        return $b->map($this->value);
    }

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
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }

    /**
     * @inheritdoc
     */
    public function bimap(callable $left, callable $right)
    {
        return self::create(call_user_func($right, $this->value));
    }
}
