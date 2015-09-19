<?php
namespace Monad\Maybe;

use Common;
use Monad;
use FantasyLand;

class Nothing implements Maybe
{
    const of = 'Monad\Maybe\None::of';

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
    public function ap(FantasyLand\ApplyInterface $applicative)
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
