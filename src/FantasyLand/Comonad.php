<?php
namespace FantasyLand;

interface Comonad extends
    FunctorInterface,
    ExtendInterface
{
    public function extract();
}
