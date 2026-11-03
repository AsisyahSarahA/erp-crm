<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Class JsonLogicEvaluator
 * Safe rule condition evaluator implementing JSON-logic specifications.
 * Strictly avoids PHP `eval()` for enterprise zero-trust security.
 */
class JsonLogicEvaluator
{
    /**
     * Evaluates a JSON-logic rule against provided data context.
     */
    public static function evaluate(mixed $logic, array $data): bool
    {
        if (!is_array($logic)) {
            return (bool) $logic;
        }

        if (empty($logic)) {
            return true;
        }

        $operator = array_key_first($logic);
        $values = $logic[$operator];

        return (bool) match ($operator) {
            '==' => self::resolveVal($values[0] ?? null, $data) == self::resolveVal($values[1] ?? null, $data),
            '===' => self::resolveVal($values[0] ?? null, $data) === self::resolveVal($values[1] ?? null, $data),
            '!=' => self::resolveVal($values[0] ?? null, $data) != self::resolveVal($values[1] ?? null, $data),
            '!==' => self::resolveVal($values[0] ?? null, $data) !== self::resolveVal($values[1] ?? null, $data),
            '>' => (float) self::resolveVal($values[0] ?? null, $data) > (float) self::resolveVal($values[1] ?? null, $data),
            '>=' => (float) self::resolveVal($values[0] ?? null, $data) >= (float) self::resolveVal($values[1] ?? null, $data),
            '<' => (float) self::resolveVal($values[0] ?? null, $data) < (float) self::resolveVal($values[1] ?? null, $data),
            '<=' => (float) self::resolveVal($values[0] ?? null, $data) <= (float) self::resolveVal($values[1] ?? null, $data),
            '!' => !self::evaluate($values[0] ?? $values, $data),
            'and' => self::evaluateAnd($values, $data),
            'or' => self::evaluateOr($values, $data),
            'in' => self::evaluateIn($values, $data),
            default => true,
        };
    }

    private static function resolveVal(mixed $val, array $data): mixed
    {
        if (is_array($val) && isset($val['var'])) {
            return data_get($data, $val['var']);
        }
        return $val;
    }

    private static function evaluateAnd(array $operands, array $data): bool
    {
        foreach ($operands as $operand) {
            if (!self::evaluate($operand, $data)) {
                return false;
            }
        }
        return true;
    }

    private static function evaluateOr(array $operands, array $data): bool
    {
        foreach ($operands as $operand) {
            if (self::evaluate($operand, $data)) {
                return true;
            }
        }
        return false;
    }

    private static function evaluateIn(array $operands, array $data): bool
    {
        $needle = self::resolveVal($operands[0] ?? null, $data);
        $haystack = self::resolveVal($operands[1] ?? null, $data);

        if (is_array($haystack)) {
            return in_array($needle, $haystack, true);
        }

        if (is_string($haystack)) {
            return str_contains($haystack, (string) $needle);
        }

        return false;
    }
}

