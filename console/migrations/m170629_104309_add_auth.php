<?php

use yii\db\Migration;

class m170629_104309_add_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'T100002',
            'psn' => 'T100000',
            'level' => '3',
            'auth_name' => '平台现金账户充值通知页面',
            'path' => '/user/bpay/brecharge/frontend-notify',
            'type' => '2',
            'auth_description' => '平台现金账户充值通知页面',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function safeDown()
    {
        echo "m170629_104309_add_auth cannot be reverted.\n";

        return false;
    }
}
