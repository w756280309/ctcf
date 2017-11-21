<?php

use yii\db\Migration;

class m171121_012543_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100701',
            'psn' => 'H100700',
            'level' => '3',
            'auth_name' => '导出',
            'path' => 'user/personalinvest/export',
            'type' => '2',
            'auth_description' => '导出excel',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m171121_012543_insert_auth cannot be reverted.\n";
        return false;
    }
}
