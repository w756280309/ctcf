<?php

use yii\db\Migration;

class m161116_064557_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101103',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '添加发行方视频',
            'path' => 'product/issuer/media-edit',
            'type' => '2',
            'auth_description' => '添加发行方视频文件',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161116_064557_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
