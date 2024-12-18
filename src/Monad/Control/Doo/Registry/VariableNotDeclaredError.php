<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Control\Doo\Registry;

use Exception;

class VariableNotDeclaredError extends Exception
{
    public function __construct(string $name)
    {
        $message = 'Variable "%s" is not declared';
        $message = sprintf($message, $name);

        parent::__construct($message);
    }
}
