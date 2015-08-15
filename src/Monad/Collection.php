<?php
namespace Monad;

use Common;
use Exception;
use Applicative;
use Functional as f;

class Collection extends Applicative\Collection implements
    MonadInterface,
    Common\ConcatInterface
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

        return static::create($result);
    }

    public function concat($value)
    {
        if ($value instanceof self) {
            return $value->concat($this->value);
        }

        return f\concat($value, $this->value);
    }
}
