<?php

use yii\db\Schema;
use yii\db\Migration;

class m150311_053612_crash extends Migration
{
    public function up()
    {
		$this->createTable('crash', [
			'id' => 'pk',
			'app_id' => 'int(11) DEFAULT NULL',
			'build_id' => 'int(11) DEFAULT NULL',
			'package_name' => 'varchar(255) NOT NULL',
			'hash' => 'varchar(255) NOT NULL',
			'hash_mini' => 'varchar(255) NOT NULL',
			'stack_trace' => 'text NOT NULL',
			'stack_trace_mini' => 'varchar(6000) NOT NULL',
			'app_version' => 'varchar(255) DEFAULT NULL',
			'user_crash_date' => 'timestamp NULL DEFAULT CURRENT_TIMESTAMP',
			'device' => 'varchar(255) DEFAULT NULL',
			'system_version' => 'varchar(255) DEFAULT NULL',
			'resolved' => 'varchar(32) DEFAULT NULL',
		]);
		$this->createIndex('IX_crash_app', 'crash', 'app_id, user_crash_date');
		$this->createIndex('IX_crash_build', 'crash', 'build_id, user_crash_date');
		$this->createIndex('IX_crash_hash', 'crash', 'hash_mini');
    }

    public function down()
    {
		$this->dropTable('crash');
    }
}
