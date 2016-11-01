<?php

use yii\db\Migration;

class m161018_091202_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100306',
            'psn' => 'H100300',
            'level' => '3',
            'auth_name' => '查看债权投资明细',
            'path' => 'user/user/credit-records',
            'type' => '2',
            'auth_description' => '查看债权投资明细',
            'status' => '1',
            'order_code' => '3',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m161018_091202_add_auth cannot be reverted.\n";

        return false;
    }
}
