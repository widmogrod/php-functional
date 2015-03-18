<?php
namespace Applicative;

use Functor;

class Just extends Functor\Just implements ApplicativeInterface
{
    /**
     * Apply applicative on applicative.
     *
     * @param ApplicativeInterface $applicative
     * @return ApplicativeInterface
     */
    public function ap(ApplicativeInterface $applicative)
    {
        return $applicative->map($this->value);
    }
}
