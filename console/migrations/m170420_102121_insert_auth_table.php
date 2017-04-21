<?php

use yii\db\Migration;

class m170420_102121_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100604',
            'psn' => 'H100600',
            'level' => '3',
            'auth_name' => '标的投资详情',
            'path' => 'user/offline/orders',
            'type' => '2',
            'auth_description' => '线下标的投资详情',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170420_102121_insert_auth_table cannot be reverted.\n";

        return false;
    }


}
