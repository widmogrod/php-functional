<?php
namespace FantasyLand;

interface ComonadInterface extends
    FunctorInterface,
    ExtendInterface
{
    public function extract();
}