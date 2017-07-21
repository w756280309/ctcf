<?php

use yii\db\Migration;

class m170720_200201_alter_online_product extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200201',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '引用',
            'path' => 'product/productonline/quote',
            'type' => '2',
            'auth_description' => '贷款管理-引用',
            'status' => '1',
            'order_code' => '2',
            'updated_at' => time(),
            'created_at' => time(),
        ]);

    }

    public function down()
    {
        echo "m170720_200201_alter_online_product be reverted.\n";

        return false;
    }
}
