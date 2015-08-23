<?php
namespace FantasyLand;

interface ChainInterface extends ApplyInterface
{
    /**
     * bind :: Monad m => (a -> m b) -> m b
     *
     * @return mixed|\Closure
     * @param callable $function
     * @return self
     */
    public function bind(callable $function);
}
