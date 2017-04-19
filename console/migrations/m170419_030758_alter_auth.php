<?php

use yii\db\Migration;

class m170419_030758_alter_auth extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'auth_name' => '商品管理',
            'auth_description' => '商品描述',
        ], [
            'auth_name' => '兑换码管理',
            'sn' => 'A101300',
        ]);
    }

    public function down()
    {
        echo "m170419_030758_alter_auth cannot be reverted.\n";

        return false;
    }
}
