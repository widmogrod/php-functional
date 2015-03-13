<?php
namespace Monad;

use Common;

class Maybe implements
    MonadInterface,
    Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;

    const create = 'Monad\Maybe::create';

    /**
     * Bind monad value to given $transformation function
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation)
    {
        if (null === $this->value) {
            return null;
        }

        return call_user_func($transformation, $this->value);
    }
}
