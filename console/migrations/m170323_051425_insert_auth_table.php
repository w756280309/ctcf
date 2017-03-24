<?php

use yii\db\Migration;

class m170323_051425_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101604',
            'psn' => 'A101600',
            'level' => '3',
            'auth_name' => '补充领取人',
            'path' => 'o2o/card/pull',
            'type' => '2',
            'auth_description' => '补充领取人',
            'status' => '1',
            'order_code' => '4',
        ]);

    }

    public function down()
    {
        echo "m170323_051425_insert_auth_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
