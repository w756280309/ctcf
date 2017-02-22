<?php

use yii\db\Migration;

class m170214_080308_update_auth_table extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'auth_name' => '修复充值数据',
        ], [
            'path' => 'user/rechargerecord/repair-data',
        ]);
    }

    public function down()
    {
        echo "m170214_080308_update_auth_table cannot be reverted.\n";

        return false;
    }
}
