<?php

declare(strict_types=1);

namespace Widmogrod\Common;

use function Widmogrod\Functional\valueOf;

trait ValueOfTrait
{
    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function extract()
    {
        return valueOf($this->value);
    }
}
