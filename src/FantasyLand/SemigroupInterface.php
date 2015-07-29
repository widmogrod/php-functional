<?php
namespace FantasyLand;

interface SemigroupInterface
{
    /**
     * Return result of applying one semigroup with another.
     *
     * @param SemigroupInterface $value
     * @return SemigroupInterface
     */
    public function concat(SemigroupInterface $value);
}