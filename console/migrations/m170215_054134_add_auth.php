<?php

use yii\db\Migration;

class m170215_054134_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200401',
            'psn' => 'P200400',
            'level' => 3,
            'auth_name' => '回款信息导出',
            'path' => 'repayment/search/export',
            'type' => 4,
            'auth_description' => '回款信息导出',
            'status' => '1',
            'order_code' => '2',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m170215_054134_add_auth cannot be reverted.\n";

        return false;
    }
}
