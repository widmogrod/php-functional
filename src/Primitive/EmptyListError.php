<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

class EmptyListError extends \Exception
{
    public function __construct(string $method)
    {
        $message = sprintf('Cannot call %s() on empty list', $method);
        parent::__construct($message);
    }
}
