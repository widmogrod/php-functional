<?php

declare(strict_types=1);

namespace Widmogrod\Monad;

use FunctionalPHP\FantasyLand;
use Widmogrod\Common;

class Identity implements
    FantasyLand\Monad,
    Common\ValueOfInterface
{
    public const of = 'Widmogrod\Monad\Identity::of';

    use Common\PointedTrait;
    use Common\ValueOfTrait;

    /**
     * @inheritdocus
     */
    public function map(callable $transformation): FantasyLand\Functor
    {
        return static::of($this->bind($transformation));
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $applicative): FantasyLand\Apply
    {
        return $applicative->map($this->value);
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        return $transformation($this->value);
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return $this->value;
    }
}
