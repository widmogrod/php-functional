<?php
namespace FantasyLand;

interface ApplyInterface extends FunctorInterface
{
    /**
     * @param ApplyInterface $b
     * @return self
     */
    public function ap(ApplyInterface $b);
}