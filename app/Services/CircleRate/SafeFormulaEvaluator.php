<?php

namespace App\Services\CircleRate;

use InvalidArgumentException;

class SafeFormulaEvaluator
{
    public function validate(string $formula): void
    {
        $identifiers = [];
        foreach ($this->tokenize($formula) as $token) {
            if ($this->isIdentifier($token)) $identifiers[$token] = 1.0;
        }
        $this->evaluate($formula, $identifiers);
    }

    public function evaluate(string $formula, array $variables): float
    {
        $tokens = $this->tokenize($formula);
        $output = [];
        $operators = [];
        $expectOperand = true;

        foreach ($tokens as $token) {
            if (is_numeric($token) || $this->isIdentifier($token)) {
                if (! $expectOperand) throw new InvalidArgumentException('Missing operator in formula.');
                $output[] = $token;
                $expectOperand = false;
                continue;
            }

            if ($token === '(') {
                if (! $expectOperand) throw new InvalidArgumentException('Missing operator before opening parenthesis.');
                $operators[] = $token;
                $expectOperand = true;
                continue;
            }

            if ($token === ')') {
                if ($expectOperand) throw new InvalidArgumentException('Unexpected closing parenthesis.');
                while ($operators && end($operators) !== '(') $output[] = array_pop($operators);
                if (! $operators) throw new InvalidArgumentException('Unmatched closing parenthesis.');
                array_pop($operators);
                $expectOperand = false;
                continue;
            }

            $operator = $token;
            if ($expectOperand && $operator === '-') $operator = 'u-';
            elseif ($expectOperand) throw new InvalidArgumentException('Unexpected operator in formula.');

            while ($operators && end($operators) !== '(') {
                $top = end($operators);
                $higher = $this->precedence($top) > $this->precedence($operator);
                $equalLeftAssociative = $this->precedence($top) === $this->precedence($operator) && $operator !== 'u-';
                if (! $higher && ! $equalLeftAssociative) break;
                $output[] = array_pop($operators);
            }
            $operators[] = $operator;
            $expectOperand = true;
        }

        if ($expectOperand) throw new InvalidArgumentException('Formula ends with an operator.');
        while ($operators) {
            $operator = array_pop($operators);
            if ($operator === '(') throw new InvalidArgumentException('Unmatched opening parenthesis.');
            $output[] = $operator;
        }

        $stack = [];
        foreach ($output as $token) {
            if (is_numeric($token)) {
                $stack[] = (float) $token;
                continue;
            }
            if ($this->isIdentifier($token)) {
                if (! array_key_exists($token, $variables) || ! is_numeric($variables[$token])) {
                    throw new InvalidArgumentException('Formula variable '.$token.' is missing or is not numeric.');
                }
                $stack[] = (float) $variables[$token];
                continue;
            }
            if ($token === 'u-') {
                if (count($stack) < 1) throw new InvalidArgumentException('Invalid unary operator.');
                $stack[] = -1 * array_pop($stack);
                continue;
            }

            if (count($stack) < 2) throw new InvalidArgumentException('Invalid formula expression.');
            $right = array_pop($stack);
            $left = array_pop($stack);
            $stack[] = match ($token) {
                '+' => $left + $right,
                '-' => $left - $right,
                '*' => $left * $right,
                '/' => $right == 0.0 ? throw new InvalidArgumentException('Division by zero in formula.') : $left / $right,
                default => throw new InvalidArgumentException('Unsupported formula operator.'),
            };
        }

        if (count($stack) !== 1 || ! is_finite($stack[0])) {
            throw new InvalidArgumentException('Formula did not produce a finite numeric result.');
        }

        return (float) $stack[0];
    }

    private function tokenize(string $formula): array
    {
        if (strlen($formula) > 300) throw new InvalidArgumentException('Formula is too long.');
        preg_match_all('/\d+(?:\.\d+)?|[A-Za-z_][A-Za-z0-9_.]*|[()+\-*\/]/', $formula, $matches);
        $tokens = $matches[0] ?? [];
        if (! $tokens || implode('', $tokens) !== preg_replace('/\s+/', '', $formula) || count($tokens) > 100) {
            throw new InvalidArgumentException('Formula contains unsupported characters or is too complex.');
        }
        return $tokens;
    }

    private function isIdentifier(string $token): bool
    {
        return (bool) preg_match('/^[A-Za-z_][A-Za-z0-9_.]*$/', $token);
    }

    private function precedence(string $operator): int
    {
        return match ($operator) {
            'u-' => 3,
            '*', '/' => 2,
            '+', '-' => 1,
            default => 0,
        };
    }
}
