<?php

namespace ContatoSeguro\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Contatoseguro\TesteBackend\Model\Comment;

class CommentTest extends TestCase
{
    public function testConstructor(): void
    {
        $comment = new Comment(
            1,
            10,
            5,
            null,
            'Comentário de teste',
            '2026-07-28 10:00:00'
        );

        $this->assertSame(1, $comment->id);
        $this->assertSame(10, $comment->productId);
        $this->assertSame(5, $comment->adminUserId);
        $this->assertNull($comment->parentCommentId);
        $this->assertSame('Comentário de teste', $comment->comment);
        $this->assertSame('2026-07-28 10:00:00', $comment->createdAt);
        $this->assertNull($comment->deletedAt);

        $this->assertSame([], $comment->getReplies());
        $this->assertSame(0, $comment->getLikes());
        $this->assertFalse($comment->isDeleted());
    }


    public function testHydrateByFetch(): void
    {
        $fetch = new \stdClass();

        $fetch->id = '1';
        $fetch->product_id = '10';
        $fetch->admin_user_id = '5';
        $fetch->parent_comment_id = '2';
        $fetch->comment = 'Comentário vindo do banco';
        $fetch->created_at = '2026-07-28 10:00:00';
        $fetch->deleted_at = null;

        $comment = Comment::hydrateByFetch($fetch);

        $this->assertSame(1, $comment->id);
        $this->assertSame(10, $comment->productId);
        $this->assertSame(5, $comment->adminUserId);
        $this->assertSame(2, $comment->parentCommentId);
        $this->assertSame(
            'Comentário vindo do banco',
            $comment->comment
        );
        $this->assertSame(
            '2026-07-28 10:00:00',
            $comment->createdAt
        );
        $this->assertNull($comment->deletedAt);
    }


    public function testHydrateByFetchWithoutParentCommentId(): void
    {
        $fetch = new \stdClass();

        $fetch->id = 1;
        $fetch->product_id = 10;
        $fetch->admin_user_id = 5;
        $fetch->comment = 'Comentário principal';
        $fetch->created_at = '2026-07-28 10:00:00';

        $comment = Comment::hydrateByFetch($fetch);

        $this->assertNull($comment->parentCommentId);
    }


    public function testSetReplies(): void
    {
        $comment = $this->createComment();

        $replies = [
            $this->createComment(2),
            $this->createComment(3)
        ];

        $comment->setReplies($replies);

        $this->assertSame($replies, $comment->getReplies());
    }


    public function testAddReply(): void
    {
        $comment = $this->createComment();

        $reply = $this->createComment(2);

        $comment->addReply($reply);

        $this->assertCount(1, $comment->getReplies());
        $this->assertSame($reply, $comment->getReplies()[0]);
    }


    public function testSetLikes(): void
    {
        $comment = $this->createComment();

        $comment->setLikes(10);

        $this->assertSame(10, $comment->getLikes());
    }


    public function testIncrementLikes(): void
    {
        $comment = $this->createComment();

        $comment->incrementLikes();
        $comment->incrementLikes();

        $this->assertSame(2, $comment->getLikes());
    }


    public function testIsDeleted(): void
    {
        $comment = new Comment(
            1,
            10,
            5,
            null,
            'Comentário removido',
            '2026-07-28 10:00:00',
            '2026-07-29 10:00:00'
        );

        $this->assertTrue($comment->isDeleted());
    }


    private function createComment(int $id = 1): Comment
    {
        return new Comment(
            $id,
            10,
            5,
            null,
            'Comentário teste',
            '2026-07-28 10:00:00'
        );
    }
}