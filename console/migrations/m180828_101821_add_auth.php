<?php

use yii\db\Migration;

class m180828_101821_add_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'E100000',
            'psn' => '0',
            'level' => '1',
            'auth_name' => '工具箱',
            'path' => 'toolbox/transfer/index',
            'type' => '1',
            'auth_description' => '工具箱',
            'status' => '1',
            'order_code' => '10',
        ]);
        $this->insert('auth', [
            'sn' => 'E100100',
            'psn' => 'E100000',
            'level' => '2',
            'auth_name' => '资金转移',
            'path' => 'toolbox/transfer/index',
            'type' => '1',
            'auth_description' => '资金转移',
            'status' => '1',
            'order_code' => '10',
        ]);
        $this->insert('auth', [
            'sn' => 'E100101',
            'psn' => 'E100100',
            'level' => '3',
            'auth_name' => '商户间转账显示余额',
            'path' => 'toolbox/transfer/get-balance',
            'type' => '2',
            'auth_description' => '与商户间确认转账配合使用',
            'status' => '1',
            'order_code' => '10',
        ]);
        $this->insert('auth', [
            'sn' => 'E100102',
            'psn' => 'E100100',
            'level' => '3',
            'auth_name' => '商户间确认转账',
            'path' => 'toolbox/transfer/first',
            'type' => '2',
            'auth_description' => '与商户间转账显示余额配合使用',
            'status' => '1',
            'order_code' => '10',
        ]);
    }

    public function safeDown()
    {
        $this->delete('auth', ['sn' => 'E100000']);
        $this->delete('auth', ['sn' => 'E100100']);
        $this->delete('auth', ['sn' => 'E100101']);
        $this->delete('auth', ['sn' => 'E100102']);
    }
}
