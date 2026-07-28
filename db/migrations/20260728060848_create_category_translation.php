<?php

use Phinx\Migration\AbstractMigration;

class CreateCategoryTranslation extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('category_translation');

        $table
            ->addColumn('category_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('lang_code', 'string', [
                'limit' => 5,
                'null' => false,
            ])
            ->addColumn('label', 'string', [
                'limit' => 255,
                'null' => false,
            ])
            ->addColumn('created_at', 'datetime', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(
                ['category_id'],
                [
                    'name' => 'idx_category_translation_category'
                ]
            )
            ->addIndex(
                ['category_id', 'lang_code'],
                [
                    'unique' => true,
                    'name' => 'uk_category_translation'
                ]
            )
            ->create();

        $table
            ->addForeignKey(
                'category_id',
                'category',
                'id',
                [
                    'delete' => 'CASCADE',
                    'update' => 'CASCADE'
                ]
            )
            ->save();
    }

    public function down(): void
    {
        $this->table('category_translation')->drop()->save();
    }
}