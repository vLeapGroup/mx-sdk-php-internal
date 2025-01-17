<?php

namespace MultiversX\Utils;

use MultiversX\Errors\ErrInvariantFailed;

class Guards
{
    public static function guardTrue(bool $value, string $what): void
    {
        if (!$value) {
            throw new ErrInvariantFailed("[<{$what}>] isn't true");
        }
    }

    public static function guardValueIsSet(string $name, mixed $value): void
    {
        self::guardValueIsSetWithMessage("{$name} isn't set (null or undefined)", $value);
    }

    public static function guardValueIsSetWithMessage(string $message, mixed $value): void
    {
        if ($value === null) {
            throw new ErrInvariantFailed($message);
        }
    }

    public static function guardSameLength(array $a, array $b): void
    {
        $a = $a ?? [];
        $b = $b ?? [];

        if (count($a) !== count($b)) {
            throw new ErrInvariantFailed("arrays do not have the same length");
        }
    }

    public static function guardLength(mixed $withLength, int $expectedLength): void
    {
        $actualLength = 0;

        if (is_string($withLength)) {
            $actualLength = strlen($withLength);
        } elseif (is_array($withLength)) {
            $actualLength = count($withLength);
        } elseif (is_object($withLength) && method_exists($withLength, 'length')) {
            $actualLength = $withLength->length();
        } elseif (is_countable($withLength)) {
            $actualLength = count($withLength);
        }

        if ($actualLength !== $expectedLength) {
            throw new ErrInvariantFailed("wrong length, expected: {$expectedLength}, actual: {$actualLength}");
        }
    }

    public static function guardNotEmpty(mixed $value, string $what): void
    {
        if (self::isEmpty($value)) {
            throw new ErrInvariantFailed("{$what} is empty");
        }
    }

    public static function guardEmpty(mixed $value, string $what): void
    {
        if (!self::isEmpty($value)) {
            throw new ErrInvariantFailed("{$what} is not empty");
        }
    }

    private static function isEmpty(mixed $value): bool
    {
        if (is_array($value)) {
            return empty($value);
        }

        if (is_object($value)) {
            if (method_exists($value, 'isEmpty')) {
                return $value->isEmpty();
            }
            if (method_exists($value, 'length')) {
                return $value->length() === 0;
            }
        }

        return empty($value);
    }
}
