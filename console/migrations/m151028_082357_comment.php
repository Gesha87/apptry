<?php

use yii\db\Schema;
use yii\db\Migration;

class m151028_082357_comment extends Migration
{
    public function up()
    {
		$this->addColumn('build', 'comment', 'VARCHAR(1024) DEFAULT NULL');
    }

    public function down()
    {
        echo "m151028_082357_comment cannot be reverted.\n";

        return false;
    }
}
