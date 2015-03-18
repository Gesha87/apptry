<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_043422_add_product_name_to_app extends Migration
{
    public function up()
    {
		$this->addColumn('app', 'product_name', 'VARCHAR(255) DEFAULT NULL');
    }

    public function down()
    {
        $this->dropColumn('app', 'product_name');
    }
}
