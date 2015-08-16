<?php
namespace Monad\Maybe;

use Common;
use Monad;
use FantasyLand;

class Nothing implements Maybe
{
    const of = 'Monad\Maybe\None::of';

    public static function of($b)
    {
        return new static();
    }

    public function ap(FantasyLand\ApplyInterface $applicative)
    {
        return $this;
    }

    public function map(callable $transformation)
    {
        return $this;
    }

    public function bind(callable $transformation)
    {
        return $this;
    }

    public function orElse(callable $fn)
    {
        return call_user_func($fn);
    }

    public function extract()
    {
        return null;
    }
}
