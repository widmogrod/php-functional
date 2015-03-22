<?php
namespace Monad;

use Common;
use Exception;
use Applicative;
use Functional as f;

class Collection extends Applicative\Collection implements
    MonadInterface,
    Feature\LiftInterface
{
    const create = 'Monad\Collection::create';

    /**
     * Bind monad value to given $transformation function
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation)
    {
        $result = [];
        foreach ($this->value as $index => $value) {
            $result = f\concat(
                $result,
                $value instanceof MonadInterface
                    ? $value->bind($transformation)
                    : call_user_func($transformation, $value, $index)
            );
        }

        return $result;
    }

    /**
     * Converts values returned by regular function to monadic value.
     *
     * @param callable $transformation
     * @return Collection
     */
    public function lift(callable $transformation)
    {
        $result = [];
        foreach ($this->value as $index => $value) {
            $result = f\concat(
                $result,
                $value instanceof MonadInterface
                    ? f\liftM($value, $transformation)
                    : call_user_func($transformation, $value, $index)
            );
        }

        return $this::create($result);
    }
}
