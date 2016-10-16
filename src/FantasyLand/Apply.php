<?php
namespace Widmogrod\FantasyLand;

interface Apply extends Functor
{
    /**
     * @param Apply $b
     * @return self
     */
    public function ap(Apply $b);
}
