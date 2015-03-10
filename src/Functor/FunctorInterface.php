<?php
namespace Functor;

interface FunctorInterface
{
    public function map(callable $transformation);
}
