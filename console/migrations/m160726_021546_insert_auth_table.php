<?php

use yii\db\Migration;

class m160726_021546_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100102',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查询联动状态',
            'path' => 'user/user/umpuserinfo',
            'type' => '2',
            'auth_description' => '查询联动状态',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m160726_021546_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
