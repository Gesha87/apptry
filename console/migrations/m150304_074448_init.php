<?php

use yii\db\Migration;

class m150304_074448_init extends Migration
{
    public function up()
    {
		$this->createTable('app', [
			'id' => 'pk',
			'name' => 'varchar(255) NOT NULL',
			'icon' => 'varchar(512) DEFAULT NULL',
			'bundle_identifier' => 'varchar(255) NOT NULL',
			'last_update' => 'timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
			'latest_build' => 'int(11) DEFAULT NULL',
		]);
		$this->createTable('build', [
			'id' => 'pk',
			'app_id' => 'int(11) DEFAULT NULL',
			'added_date' => 'timestamp NULL DEFAULT CURRENT_TIMESTAMP',
			'hash' => 'varchar(255) NOT NULL',
			'version' => 'varchar(255) NOT NULL',
			'plist' => 'varchar(512) NOT NULL',
			'count_crashes' => 'int(11) DEFAULT 0',
		]);
		$this->createIndex('IX_build_version', 'build', 'version');
		$this->createIndex('IX_build_hash', 'build', 'hash');
    }

    public function down()
    {
		$this->dropTable('build');
		$this->dropTable('app');
    }
}
