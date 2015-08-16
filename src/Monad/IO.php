<?php
namespace Monad;

use FantasyLand\ApplyInterface;
use FantasyLand\MonadInterface;

class IO implements MonadInterface
{


    /**
     * @param ApplyInterface $b
     * @return self
     */
    public function ap(ApplyInterface $b)
    {

    }

    /**
     * @param callable $function
     * @return self
     */
    public function bind(callable $function)
    {

    }

    /**
     * @param callable $function
     * @return self
     */
    public function map(callable $function)
    {

    }
}
