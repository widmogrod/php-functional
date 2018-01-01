<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo\Registry;

class CannotRedeclareVariableError extends \Exception
{
    public function __construct(string $name, array $registered)
    {
        $message = 'Cannot redeclare variable "%s". Registered variables %s';
        $message = sprintf($message, $name, join(',', $registered));
        parent::__construct($message);
    }
}
