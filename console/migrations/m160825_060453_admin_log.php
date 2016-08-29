<?php

use yii\db\Migration;

class m160825_060453_admin_log extends Migration
{
    public function up()
    {
        $this->createTable('admin_log', [
            'id' => $this->primaryKey(),
            'admin_id' => $this->string(10),
            'created_at' => $this->integer(),
            'ip' => $this->string(30),
            'tableName' => $this->string(30),
            'primaryKey' => $this->string(32),
            'allAttributes' => $this->text(),
            'changeSet' => $this->text(),
        ]);
        $this->createIndex('admin_log_table_name', 'admin_log', 'tableName');
        $this->createIndex('admin_log_admin_id', 'admin_log', 'admin_id');
        $this->createIndex('admin_log_primary_key', 'admin_log', 'primaryKey');
    }

    public function down()
    {
        echo "m160825_060453_admin_log cannot be reverted.\n";

        return false;
    }
}
