<?php

declare(strict_types=1);

namespace Widmogrod\Common;

trait ValueOfTrait
{
    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function extract()
    {
        return \Widmogrod\Functional\valueOf($this->value);
    }
}
