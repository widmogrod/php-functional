<?php
namespace Monad;

interface BindInterface 
{
    /**
     * Bind monad value to given $transformation function.
     *
     * @param callable $transformation
     * @return mixed
     */
    public function bind(callable $transformation);
}