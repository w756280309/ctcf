<?php

use yii\db\Migration;

class m170308_074947_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100205',
            'psn' => 'H100200',
            'level' => '3',
            'auth_name' => '查看融资方联动账户余额',
            'path' => 'user/user/ump-org-account',
            'type' => '2',
            'auth_description' => '查看融资方联动账户余额',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170308_074947_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
