<?php

use Phinx\Migration\AbstractMigration;

class AddChangedFieldToProductLog extends AbstractMigration
{
    public function up(): void
    {
        $table = $this->table('product_log');

        $table
            ->addColumn('changed_field', 'string', [
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('old_value', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->update();
    }

    public function down(): void
    {
        $table = $this->table('product_log');

        $table
            ->removeColumn('changed_field')
            ->removeColumn('old_value')
            ->update();
    }
}
