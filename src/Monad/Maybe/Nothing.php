<?php

namespace Widmogrod\Monad\Maybe;

use Widmogrod\FantasyLand;

class Nothing implements Maybe
{
    const of = 'Widmogrod\Monad\Maybe\Nothing::of';

    /**
     * @inheritdoc
     */
    public static function of($value)
    {
        return new static();
    }

    /**
     * @inheritdoc
     */
    public function ap(FantasyLand\Apply $applicative)
    {
        return $this;
    }

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
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function orElse(callable $fn)
    {
        return call_user_func($fn);
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return null;
    }
}
