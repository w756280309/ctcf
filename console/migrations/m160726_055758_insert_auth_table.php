<?php

use yii\db\Migration;

class m160726_055758_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100103',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查询流水在联动状态',
            'path' => 'user/rechargerecord/get-order-status',
            'type' => '2',
            'auth_description' => '查询流水在联动状态',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m160726_055758_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
