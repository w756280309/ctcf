<?php

use yii\db\Migration;

class m180530_115414_insert_into_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'P200500',
            'psn' => 'P200104',
            'level' => '4',
            'auth_name' => '易保全合同下载',
            'path' => 'order/onlineorder/ebaoquan',
            'type' => '2',
            'auth_description' => '易保全合同下载',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function safeDown()
    {
        echo "m180530_115414_insert_into_auth cannot be reverted.\n";

        return false;
    }
}
