<?php
namespace Applicative;

use Functor;

class Identity extends Functor\Identity implements ApplicativeInterface
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
