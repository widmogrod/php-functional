<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

class TypeMismatchError extends \Exception
{
    public function __construct($value, $expected)
    {
        $givenType = is_object($value) ? get_class($value) : gettype($value);
        $message = 'Expected type is %s but given %s';
        $message = sprintf($message, $expected, $givenType);
        parent::__construct($message);
    }
}
