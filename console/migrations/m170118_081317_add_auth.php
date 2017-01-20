<?php

use yii\db\Migration;

class m170118_081317_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101308',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '添加商品',
            'path' => 'growth/code/goods-add',
            'type' => '2',
            'auth_description' => '添加商品',
            'status' => '1',
            'order_code' => '4',
        ]);

    }

    public function down()
    {
        echo "m170118_081317_add_auth cannot be reverted.\n";

        return false;
    }
}
