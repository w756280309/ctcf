<?php

use yii\db\Migration;

class m170906_110057_add_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'P200203',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '标的订单交易凭证',
            'path' => 'product/growth/order-cert',
            'type' => '2',
            'auth_description' => '标的订单交易凭证',
            'status' => '1',
            'order_code' => '2',
        ]);
        $this->insert('auth', [
            'sn' => 'P200302',
            'psn' => 'P200300',
            'level' => '3',
            'auth_name' => '转让订单交易凭证',
            'path' => 'product/growth/transfer-cert',
            'type' => '2',
            'auth_description' => '转让订单交易凭证',
            'status' => '1',
            'order_code' => '2',
        ]);
    }

    public function safeDown()
    {
        echo "m170906_110057_add_auth cannot be reverted.\n";

        return false;
    }
}
