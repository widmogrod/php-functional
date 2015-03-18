<?php
namespace Functor;

use Common;

interface FunctorInterface extends Common\CreateInterface
{
    /**
     * Transforms one category into another category.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function map(callable $transformation);
}
