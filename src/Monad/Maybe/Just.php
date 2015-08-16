<?php
namespace Monad\Maybe;

use Common;
use Monad;
use Functor;
use FantasyLand;
use Applicative;

class Just implements Maybe
{
    use Common\PointedTrait;

    const of = 'Monad\Maybe\Just::of';

    public function ap(FantasyLand\ApplyInterface $applicative)
    {
        return $applicative->map($this->value);
    }

    public function map(callable $transformation)
    {
        return $this->bind(function ($value) use ($transformation) {
            return self::of(call_user_func($transformation, $value));
        });
    }

    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }

    public function orElse(callable $fn)
    {
        return $this;
    }

    public function extract()
    {
        return $this->value;
    }
}
