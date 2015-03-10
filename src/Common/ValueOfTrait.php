<?php
namespace Common;

trait ValueOfTrait 
{
    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function valueOf()
    {
        return $this->value;
    }
}
