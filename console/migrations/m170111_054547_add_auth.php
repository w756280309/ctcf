<?php

use yii\db\Migration;

class m170111_054547_add_auth extends Migration
{
    public function up()
    {
        $this->update('auth', ['sn' => 'H100600'], ['sn' => 'H100501']);
        $this->insert('auth', [
            'sn' => 'H100601',
            'psn' => 'H100600',
            'level' => '3',
            'auth_name' => '查询积分流水',
            'path' => 'user/offline/points',
            'type' => '2',
            'auth_description' => '线下积分流水明细',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100602',
            'psn' => 'H100600',
            'level' => '3',
            'auth_name' => '兑换商品',
            'path' => 'user/offline/exchange-goods',
            'type' => '2',
            'auth_description' => '兑换商品',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100603',
            'psn' => 'H100600',
            'level' => '3',
            'auth_name' => '确认兑换商品',
            'path' => 'user/offline/do-exchange',
            'type' => '2',
            'auth_description' => '确认兑换商品',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170111_054547_add_auth cannot be reverted.\n";

        return false;
    }
}
