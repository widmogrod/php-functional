<?php
namespace Common;

trait ValueOfTrait 
{
    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function extract()
    {
        return \Functional\valueOf($this->value);
    }
}
