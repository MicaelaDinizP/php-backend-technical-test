<?php

namespace Contatoseguro\TesteBackend\Service;

use Contatoseguro\TesteBackend\Config\DB;
use Contatoseguro\TesteBackend\Model\Comment;
use Contatoseguro\TesteBackend\Validation\CommentValidator;
use PDO;
use PDOException;

class CommentService
{
    private PDO $pdo;

    private CommentValidator $validator;

    public function __construct()
    {
        $this->pdo = DB::connect();
        $this->validator = new CommentValidator();
    }


    public function create(
    int $productId,
    array $body,
    int $adminUserId
): ServiceResult {

    $validation = $this->validator->validateCreate($body);

    if ($validation->hasErrors()) {
        return ServiceResult::validationError($validation);
    }

    if (!$this->productExists($productId)) {
        return ServiceResult::failure(
            'Product not found.',
            404
        );
    }

    try {

        $stm = $this->pdo->prepare("
            INSERT INTO comments (
                product_id,
                admin_user_id,
                comment,
                created_at
            )
            VALUES (
                :product_id,
                :admin_user_id,
                :comment,
                :created_at
            )
        ");

        $stm->execute([
            ':product_id' => $productId,
            ':admin_user_id' => $adminUserId,
            ':comment' => trim($body['comment']),
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        $id = (int) $this->pdo->lastInsertId();

        return ServiceResult::success(
    data: [
        'id' => $id
    ],
    status: 201,
    message: 'Comment created successfully.'
);

    } catch (PDOException $exception) {

        return ServiceResult::failure(
            'Unable to create comment.',
            500
        );
    }
}

    public function reply(
    int $productId,
    int $commentId,
    array $body,
    int $adminUserId
): ServiceResult {

    $validation = $this->validator->validateReply($body);

    if ($validation->hasErrors()) {
        return ServiceResult::validationError($validation);
    }

    $parent = $this->find($commentId);

    if (!$parent) {
        return ServiceResult::failure(
            'Comment not found.',
            404
        );
    }

    if ($parent->productId !== $productId) {
        return ServiceResult::failure(
            'Comment does not belong to this product.',
            400
        );
    }

    try {

        $stm = $this->pdo->prepare("
            INSERT INTO comments (
                product_id,
                admin_user_id,
                parent_comment_id,
                comment,
                created_at
            )
            VALUES (
                :product_id,
                :admin_user_id,
                :parent_comment_id,
                :comment,
                :created_at
            )
        ");

        $stm->execute([
            ':product_id' => $productId,
            ':admin_user_id' => $adminUserId,
            ':parent_comment_id' => $commentId,
            ':comment' => trim($body['comment']),
            ':created_at' => date('Y-m-d H:i:s')
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return ServiceResult::success(
    data: [
        'id' => $id
    ],
    status: 201,
    message: 'Reply created successfully.'
);

    } catch (PDOException $exception) {

        return ServiceResult::failure(
            'Unable to create reply.',
            500
        );
    }
}

    public function delete(
        int $commentId,
        int $adminUserId
    ): ServiceResult {

        $comment = $this->find($commentId);


        if (!$comment) {
            return ServiceResult::failure(
                'Comment not found.',
                404
            );
        }
if ($comment->isDeleted()) {
    return ServiceResult::failure(
        'Comment already deleted.',
        409
    );
}

        if ($comment->adminUserId !== $adminUserId) {

            return ServiceResult::failure(
                'You cannot delete another user comment.',
                403
            );
        }


        $stm = $this->pdo->prepare("
            UPDATE comments
            SET deleted_at = :deleted_at
            WHERE id = :id
        ");


        $stm->execute([
            ':deleted_at' => date('Y-m-d H:i:s'),
            ':id' => $commentId
        ]);


        return ServiceResult::success(
            status: 204,
            message: 'Comment deleted successfully.'
        );
    }


    public function like(
        int $commentId,
        int $adminUserId
    ): ServiceResult {


        if (!$this->find($commentId)) {

            return ServiceResult::failure(
                'Comment not found.',
                404
            );
        }


        try {

            $stm = $this->pdo->prepare("
                INSERT INTO comment_like (
                    comment_id,
                    admin_user_id,
                    created_at
                )
                VALUES (
                    :comment_id,
                    :admin_user_id,
                    :created_at
                )
            ");


            $stm->execute([
                ':comment_id' => $commentId,
                ':admin_user_id' => $adminUserId,
                ':created_at' => date('Y-m-d H:i:s')
            ]);


            return ServiceResult::success(
                status: 201,
                message: 'Comment liked successfully.'
            );


        } catch (PDOException $exception) {


            if (str_contains(
                $exception->getMessage(),
                'UNIQUE'
            )) {

                return ServiceResult::failure(
                    'Comment already liked.',
                    409
                );
            }


            return ServiceResult::failure(
                'Unable to like comment.',
                500
            );
        }
    }


    public function getProductComments(
        int $productId
    ): ServiceResult {


        $stm = $this->pdo->prepare("
            SELECT
                c.*,
                COUNT(cl.id) AS likes
            FROM comments c
            LEFT JOIN comment_like cl
                ON cl.comment_id = c.id
            WHERE c.product_id = :product_id
            AND c.deleted_at IS NULL
            GROUP BY c.id
            ORDER BY
    c.parent_comment_id,
    c.created_at ASC
        ");


        $stm->execute([
            ':product_id' => $productId
        ]);


        $comments = [];


        foreach ($stm->fetchAll() as $row) {

            $comment = Comment::hydrateByFetch($row);

            $comment->setLikes(
                (int) $row->likes
            );

            $comments[$comment->id] = $comment;
        }


        $tree = [];


        foreach ($comments as $comment) {

            if ($comment->parentCommentId === null) {

                $tree[] = $comment;

                continue;
            }


            if (isset($comments[$comment->parentCommentId])) {

                $comments[$comment->parentCommentId]
                    ->addReply($comment);
            }
        }


        return ServiceResult::success(
            data: $tree
        );
    }


    private function find(int $id): ?Comment
    {
        $stm = $this->pdo->prepare("
    SELECT *
    FROM comments
    WHERE id = :id
      AND deleted_at IS NULL
");


        $stm->execute([
            ':id' => $id
        ]);


        $comment = $stm->fetch();


        if (!$comment) {
            return null;
        }


        return Comment::hydrateByFetch($comment);
    }


    private function productExists(int $productId): bool
    {
        $stm = $this->pdo->prepare("
            SELECT COUNT(*)
            FROM product
            WHERE id = :id
        ");


        $stm->execute([
            ':id' => $productId
        ]);


        return (int) $stm->fetchColumn() > 0;
    }
}