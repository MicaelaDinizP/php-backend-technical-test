<?php

namespace Contatoseguro\TesteBackend\Model;

class Comment
{
    private array $replies = [];

    private int $likes = 0;

    public function __construct(
        public int $id,
        public int $productId,
        public int $adminUserId,
        public ?int $parentCommentId,
        public string $comment,
        public string $createdAt,
        public ?string $deletedAt = null
    ) {
    }

    public static function hydrateByFetch(object $fetch): self
    {
        return new self(
            id: (int) $fetch->id,
            productId: (int) $fetch->product_id,
            adminUserId: (int) $fetch->admin_user_id,
            parentCommentId: isset($fetch->parent_comment_id)
                ? (int) $fetch->parent_comment_id
                : null,
            comment: (string) $fetch->comment,
            createdAt: (string) $fetch->created_at,
            deletedAt: $fetch->deleted_at ?? null
        );
    }

    public function getReplies(): array
    {
        return $this->replies;
    }

    public function setReplies(array $replies): void
    {
        $this->replies = $replies;
    }

    public function addReply(Comment $reply): void
    {
        $this->replies[] = $reply;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }

    public function incrementLikes(): void
    {
        $this->likes++;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}