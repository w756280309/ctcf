<?php

use yii\db\Migration;

class m170113_071200_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100308',
            'psn' => 'H100300',
            'level' => '3',
            'auth_name' => '线上积分明细',
            'path' => 'user/user/point-list',
            'type' => '2',
            'auth_description' => '线上积分明细',
            'status' => '1',
            'order_code' => '3',
        ]);

        $this->insert('auth', [
            'sn' => 'H100309',
            'psn' => 'H100300',
            'level' => '3',
            'auth_name' => '线上、线下财富值明细',
            'path' => 'user/user/coin-list',
            'type' => '2',
            'auth_description' => '线上、线下财富值明细',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170113_071200_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
