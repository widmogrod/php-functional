<?php
namespace Applicative;

use Functor;

interface ApplicativeInterface extends Functor\FunctorInterface
{
    /**
     * Apply applicative on applicative.
     *
     * @param ApplicativeInterface $applicative
     * @return ApplicativeInterface
     */
    public function ap(ApplicativeInterface $applicative);
}
