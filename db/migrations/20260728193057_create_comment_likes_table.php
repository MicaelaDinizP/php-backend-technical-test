<?php

use Phinx\Migration\AbstractMigration;

class CreateCommentLikesTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('comment_like');

        $table
            ->addColumn('comment_id', 'integer', [
                'null' => false
            ])
            ->addColumn('admin_user_id', 'integer', [
                'null' => false
            ])
            ->addColumn('created_at', 'datetime', [
                'null' => false
            ])

            ->addForeignKey(
                'comment_id',
                'comments',
                'id',
                [
                    'delete' => 'CASCADE',
                    'update' => 'NO_ACTION'
                ]
            )

            ->addForeignKey(
                'admin_user_id',
                'admin_user',
                'id',
                [
                    'delete' => 'CASCADE',
                    'update' => 'NO_ACTION'
                ]
            )

            ->addIndex(
                ['comment_id', 'admin_user_id'],
                [
                    'unique' => true,
                    'name' => 'idx_comment_like_unique'
                ]
            )

            ->create();
    }

    public function down()
    {
        $this->table('comment_like')->drop()->save();
    }
}