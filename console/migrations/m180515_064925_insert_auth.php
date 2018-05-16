<?php

use yii\db\Migration;

class m180515_064925_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200501',
            'psn' => 'P200104',
            'level' => '4',
            'auth_name' => '下载保全合同',
            'path' => 'order/onlineorder/miit-baoquan',
            'type' => '2',
            'auth_description' => '重新生成和签保全',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m180515_064925_insert_auth cannot be reverted.\n";

        return false;
    }
}
