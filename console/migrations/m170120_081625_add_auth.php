<?php

use yii\db\Migration;

class m170120_081625_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100310',
            'psn' => 'H100300',
            'level' => 3,
            'path' => 'user/rechargerecord/repair-data',
            'type' => 2,
            'auth_description' => '修复充值数据',
            'status' => 1,
            'order_code' => 3,
        ]);
    }

    public function down()
    {
        echo "m170120_081625_add_auth cannot be reverted.\n";

        return false;
    }
}
