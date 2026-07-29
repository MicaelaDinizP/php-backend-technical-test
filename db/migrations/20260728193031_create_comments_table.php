<?php

use Phinx\Migration\AbstractMigration;

class CreateCommentsTable extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('comments');

        $table
            ->addColumn('product_id', 'integer', [
                'null' => false
            ])
            ->addColumn('admin_user_id', 'integer', [
                'null' => false
            ])
            ->addColumn('parent_comment_id', 'integer', [
                'null' => true
            ])
            ->addColumn('comment', 'text', [
                'null' => false
            ])
            ->addColumn('created_at', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP'
            ])
            ->addColumn('deleted_at', 'datetime', [
                'null' => true
            ])

            ->addForeignKey(
                'product_id',
                'product',
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

            ->addForeignKey(
                'parent_comment_id',
                'comments',
                'id',
                [
                    'delete' => 'CASCADE',
                    'update' => 'NO_ACTION'
                ]
            )

            ->create();
    }

    public function down()
    {
        $this->table('comments')->drop()->save();
    }
}