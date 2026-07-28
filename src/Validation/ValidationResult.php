<?php

namespace Contatoseguro\TesteBackend\Validation;

class ValidationResult
{
    /**
     * @var array<string, string[]>
     */
    private array $errors = [];

    public function add(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getFirstError(): ?string
    {
        foreach ($this->errors as $messages) {
            return $messages[0];
        }

        return null;
    }

    public function clear(): void
    {
        $this->errors = [];
    }
}