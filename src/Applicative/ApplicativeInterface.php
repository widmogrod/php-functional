<?php
namespace Applicative;

use Functor;

interface ApplicativeInterface extends Functor\FunctorInterface
{
    /**
     * @param ApplicativeInterface $applicative
     * @return ApplicativeInterface
     */
    public function ap(ApplicativeInterface $applicative);
}
