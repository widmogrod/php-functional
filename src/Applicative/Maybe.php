<?php
namespace Applicative;

use Functor;

class Maybe extends Functor\Maybe implements ApplicativeInterface
{
    /**
     * Apply applicative on applicative.
     *
     * @param ApplicativeInterface $applicative
     * @return ApplicativeInterface
     */
    public function ap(ApplicativeInterface $applicative)
    {
        return null === $this->value
            ? $this
            : $applicative->map($this->value);
    }
}
