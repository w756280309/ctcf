<?php

use yii\db\Migration;

class m170109_104833_add_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101103',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '首页精选项目管理',
            'path' => 'product/choice/edit',
            'type' => '2',
            'auth_description' => '首页精选项目管理',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170109_104833_add_auth_table cannot be reverted.\n";

        return false;
    }
}
