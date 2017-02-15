<?php

use yii\db\Migration;

class m170214_023849_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200400',
            'psn' => 'P200000',
            'level' => 2,
            'auth_name' => '回款查询',
            'path' => 'repayment/search/index',
            'type' => 2,
            'auth_description' => '回款查询',
            'status' => '1',
            'order_code' => '2',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m170214_023849_add_auth cannot be reverted.\n";

        return false;
    }
}
