<?php

use yii\db\Migration;

class m170509_015459_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A102000',
            'psn' => 'A100000',
            'level' => 2,
            'auth_name' => '数据导出',
            'path' => 'growth/export/index',
            'type' => 1,
            'auth_description' => '数据导出',
            'status' => 1,
            'order_code' => 4,
        ]);

        $this->insert('auth', [
            'sn' => 'A102001',
            'psn' => 'A102000',
            'level' => 3,
            'auth_name' => '导出结果',
            'path' => 'growth/export/result',
            'type' => 2,
            'auth_description' => '导出结果',
            'status' => 1,
            'order_code' => 4,
        ]);

        $this->insert('auth', [
            'sn' => 'A102002',
            'psn' => 'A102000',
            'level' => 3,
            'auth_name' => '发起导出',
            'path' => 'growth/export/confirm',
            'type' => 2,
            'auth_description' => '发起导出',
            'status' => 1,
            'order_code' => 4,
        ]);

        $this->insert('auth', [
            'sn' => 'A102003',
            'psn' => 'A102000',
            'level' => 3,
            'auth_name' => '文件下载',
            'path' => 'growth/export/download',
            'type' => 2,
            'auth_description' => '文件下载',
            'status' => 1,
            'order_code' => 4,
        ]);


    }

    public function down()
    {
        echo "m170509_015459_add_auth cannot be reverted.\n";

        return false;
    }
}
