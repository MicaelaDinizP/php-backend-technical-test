<?php

use Phinx\Migration\AbstractMigration;

class AddStockToProduct extends AbstractMigration
{
  public function up()
  {
    $table = $this->table('product');

        $table->addColumn('stock', 'integer', [
            'default' => 0,
            'null' => false
        ]);

        $table->update();
  }

  public function down()
  {
    $table = $this->table('product');

        $table->removeColumn('stock');

        $table->update();
  }
}