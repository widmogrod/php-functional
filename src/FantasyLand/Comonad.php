<?php
namespace FantasyLand;

interface Comonad extends
    Functor,
    Extend
{
    public function extract();
}
