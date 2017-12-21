<?php

declare(strict_types=1);

namespace Widmogrod\Common;

interface ValueOfInterface
{
    /**
     * Return value wrapped by Monad
     *
     * @return mixed
     */
    public function extract();
}
