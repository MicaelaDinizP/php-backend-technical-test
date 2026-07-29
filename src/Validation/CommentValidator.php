<?php

namespace Contatoseguro\TesteBackend\Validation;

class CommentValidator
{
    private const MAX_COMMENT_LENGTH = 1000;

    public function validateCreate(array $data): ValidationResult
    {
        $result = new ValidationResult();

        $this->validateComment($data, $result);

        return $result;
    }

    public function validateReply(array $data): ValidationResult
    {
        $result = new ValidationResult();

        $this->validateComment($data, $result);

        return $result;
    }

    private function validateComment(array $data, ValidationResult $result): void
    {
        if (!isset($data['comment'])) {
            $result->add(
                'comment',
                'The comment field is required.'
            );

            return;
        }

        $comment = trim((string) $data['comment']);

        if ($comment === '') {
            $result->add(
                'comment',
                'The comment cannot be empty.'
            );
        }

        if (mb_strlen($comment) > self::MAX_COMMENT_LENGTH) {
            $result->add(
                'comment',
                sprintf(
                    'The comment cannot exceed %d characters.',
                    self::MAX_COMMENT_LENGTH
                )
            );
        }
    }
}