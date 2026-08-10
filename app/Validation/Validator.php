<?php

namespace App\Validation;

use App\Exceptions\ValidationException;

final class Validator
{
    private array $errors = [];

    public function required(string $field, mixed $value): self
    {
        if ($value === null || (is_string($value) && trim($value) === '')) {
            $this->errors[$field][] = 'Campo obrigatório.';
        }
        return $this;
    }

    public function email(string $field, mixed $value): self
    {
        if ($value !== null && $value !== '' && !filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = 'E-mail inválido.';
        }
        return $this;
    }

    public function minLength(string $field, mixed $value, int $min): self
    {
        if (is_string($value) && mb_strlen($value) < $min) {
            $this->errors[$field][] = "Deve possuir pelo menos {$min} caracteres.";
        }
        return $this;
    }

    public function numericRange(string $field, mixed $value, float $min, float $max): self
    {
        if (!is_numeric($value) || (float) $value < $min || (float) $value > $max) {
            $this->errors[$field][] = "Valor deve estar entre {$min} e {$max}.";
        }
        return $this;
    }

    public function oneOf(string $field, mixed $value, array $allowed): self
    {
        if (!in_array($value, $allowed, true)) {
            $this->errors[$field][] = 'Valor não permitido.';
        }
        return $this;
    }

    public function validate(): void
    {
        if ($this->errors !== []) {
            throw new ValidationException($this->errors);
        }
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
