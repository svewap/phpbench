<?php

namespace PhpBench\Assertion\Exception;

use RuntimeException;
use Throwable;

class ExpressionError extends RuntimeException
{
    public static function forExpression(string $expression, string $message, ?Throwable $previous = null): self
    {
        $out = [''];
        $out[] = $expression;
        $out[] = str_repeat('-', strlen($expression));
        $out[] = $message;

        return new self(implode("\n", $out), 0, $previous);
    }
}