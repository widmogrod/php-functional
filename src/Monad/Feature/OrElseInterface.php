<?php
namespace Monad\Feature;

interface OrElseInterface
{
    /**
     * Handle situation when error occur in monad computation chain.
     *
     * @param callable $fn
     * @return mixed
     */
    public function orElse(callable $fn);
}
