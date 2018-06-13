<?php

declare(strict_types=1);

namespace Widmogrod\Useful;

class PatternNotMatchedError extends \Exception
{
    public static function cannotMatch($value, array $patterns): self
    {
        $givenType = is_object($value) ? get_class($value) : gettype($value);
        $message = 'Cannot match "%s" type. Defined patterns are: "%s"';
        $message = sprintf(
            $message,
            $givenType,
            implode('", "', $patterns)
        );

        return new self($message);
    }

    public static function noPatterns($value): self
    {
        $givenType = is_object($value) ? get_class($value) : gettype($value);
        $message = 'Cannot match "%s" type. List of patterns is empty.';
        $message = sprintf(
            $message,
            $givenType
        );

        return new self($message);
    }

    public static function tupleMismatch($patternCount, $valueCount)
    {
        $message = 'tupleMismatch(%d != %d)';
        $message = sprintf($message, $patternCount, $valueCount);

        return new self($message);
    }
}
