<?php

use yii\db\Migration;

class m160629_084500_update_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'D100200',
            'psn' => 'D100000',
            'level' => '2',
            'auth_name' => '立合旺通数据',
            'path' => 'datatj/issuer/list?id=1',
            'type' => '1',
            'auth_description' => '立合旺通数据统计',
            'status' => '1',
            'order_code' => '5',
        ]);

        $this->insert('auth', [
            'sn' => 'D100201',
            'psn' => 'D100200',
            'level' => '3',
            'auth_name' => '导出数据',
            'path' => 'datatj/issuer/export',
            'type' => '1',
            'auth_description' => '立合旺通数据导出',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    public function down()
    {
        echo "m160629_084500_update_auth_table cannot be reverted.\n";

        return false;
    }
}
