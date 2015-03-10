<?php
namespace Monad;

use Common;

interface MonadInterface extends Common\CreateInterface
{
    /**
     * Bind monad value to given $transformation function.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function bind(callable $transformation);
}
