<?php
namespace FantasyLand;

interface MonoidInterface extends SemigroupInterface
{
    /**
     * Return identity element for given semigroup
     *
     * @return MonoidInterface
     */
    public function getEmpty();
}