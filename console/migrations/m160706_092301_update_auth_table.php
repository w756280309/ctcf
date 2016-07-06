<?php

use yii\db\Migration;

class m160706_092301_update_auth_table extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'sn' => 'L100100',
            'psn' => 'L100000',
            'order_code' => '6',
        ], ['path' => 'datatj/issuer/lh-list']);

        $this->update('auth', [
            'sn' => 'L100101',
            'psn' => 'L100100',
            'order_code' => '6',
        ], ['path' => 'datatj/issuer/export']);

        $this->insert('auth', [
            'sn' => 'L100000',
            'psn' => '0',
            'level' => '1',
            'auth_name' => '立合旺通数据',
            'path' => 'datatj/issuer/lh-list',
            'type' => '1',
            'auth_description' => '立合旺通数据统计',
            'status' => '1',
            'order_code' => '6',
        ]);
    }

    public function down()
    {
        echo "m160706_092301_update_auth_table cannot be reverted.\n";

        return false;
    }
}
