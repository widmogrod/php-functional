<?php
namespace Widmogrod\FantasyLand;

interface Comonad extends
    Functor,
    Extend
{
    public function extract();
}
