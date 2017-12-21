<?php

declare(strict_types=1);

namespace Widmogrod\Monad;

use Widmogrod\Common;
use Widmogrod\FantasyLand;

class Identity implements
    FantasyLand\Monad,
    Common\ValueOfInterface
{
    const of = 'Widmogrod\Monad\Identity::of';

    use Common\PointedTrait;
    use Common\ValueOfTrait;

    /**
     * @inheritdocus
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
