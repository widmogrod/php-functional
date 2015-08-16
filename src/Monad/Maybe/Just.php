<?php
namespace Monad\Maybe;

use Common;
use Monad;
use Functor;
use Applicative;

class Just implements Maybe
{
    use Common\CreateTrait;

    const create = 'Monad\Maybe\Just::create';

    public function ap(Applicative\ApplicativeInterface $applicative)
    {
        return $applicative->map($this->value);
    }

    public function map(callable $transformation)
    {
        return $this->bind(function ($value) use ($transformation) {
            return self::create(call_user_func($transformation, $value));
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

    public function valueOf()
    {
        return $this->value;
    }
}
