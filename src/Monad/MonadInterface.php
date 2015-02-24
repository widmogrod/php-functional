<?php
namespace Monad;

interface MonadInterface
{
    /**
     * Bind monad value to given $transformation function
     *
     * @param callable $transformation
     * @return MonadInterface|mixed
     */
    public function bind(callable $transformation);
}
