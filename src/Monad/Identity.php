<?php
namespace Monad;

use Common;
use Applicative;

class Identity extends Applicative\Identity implements MonadInterface
{
    const create = 'Monad\Identity::create';

    /**
     * Bind monad value to given $transformation function
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation)
    {
        return call_user_func($transformation, $this->value);
    }
}
