<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Validation\ValidationResult;

class ServiceResult
{
    public function __construct(
        public bool $success,
        public int $status,
        public mixed $data = null,
        public ?string $message = null,
        public array $errors = []
    ) {
    }

    public static function success(
        mixed $data = null,
        int $status = 200,
        ?string $message = null
    ): self {
        return new self(
            success: true,
            status: $status,
            data: $data,
            message: $message
        );
    }

    public static function failure(
        string $message,
        int $status = 400,
        array $errors = []
    ): self {
        return new self(
            success: false,
            status: $status,
            message: $message,
            errors: $errors
        );
    }

    public static function validationError(
        ValidationResult $validation,
        int $status = 400
    ): self {
        return new self(
            success: false,
            status: $status,
            message: 'Validation failed.',
            errors: $validation->getErrors()
        );
    }
}